<?php

namespace app\controllers;

use app\models\Parameter;
use Yii;
use app\models\Staff;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use app\models\Booking;
use app\models\Simulator;
use app\models\Timeslot;
use DateTime;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class StaffController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [//Allow access to logout only if user is logged-in
                'class' => AccessControl::className(),
                'except' => ['login', 'update', 'recover'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'view', 'create'],
                        'allow' => true,
                        'roles' => ['manageStaff']
                    ],
                    [
                        'actions' => ['agenda'],
                        'allow' => true,
                        'roles' => ['manageBookings', 'assignedToBooking']
                    ],
                ],
            ],
        ];
    }

    /**
     * Login
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        //Already logged in users should not access this page
        if (!Yii::$app->user->isGuest) {
            return $this->goBack();
        }
        $loginData = Yii::$app->request->post('Staff');

        //No data sent, show the form, link the controller with the view
        if (empty($loginData)) {
            return $this->render('login', [
                'model' => new Staff(),
            ]);
        } else {
            $staff = Staff::findOne(['user_name' => $loginData['user_name']]);
            if (!empty($staff) and $staff->isValidPassword($loginData['password'])) {

                if ($staff->disabled) {
                    $staff = new Staff();
                    $staff->user_name = $loginData['user_name'];
                    $staff->addError('disabled', \Yii::t('app', 'This account is disabled, you can\'t login'));
                    return $this->render('login', [
                        'model' => $staff,
                    ]);
                } else {
                    Yii::$app->user->login($staff, 3600 * 24 * 30);
                    return $this->redirect('agenda');
                }
            } else {
                $staff = new Staff();
                $staff->user_name = $loginData['user_name'];
                $staff->addError('', \Yii::t('app', 'Invalid Username or Password'));
                return $this->render('login', [
                    'model' => $staff,
                ]);
            }
        }
    }

    /**
     * Recover
     * This function controls the recovery process
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionRecover()
    {
        $recover = Yii::$app->request->post('identificator');
        $error='';
        $confirm='';

        if (!empty($recover)) {
            $staff = Staff::find()
                ->where(['user_name' => $recover])
                ->orWhere(['email' => $recover])
                ->one();

            if (!empty($staff)) {
                // email has been found, send recovery email

                $this->sendRecovery($staff);
                $confirm = Yii::t('app', 'Recovery E-Mail has been sent');
            } else{
                $error = Yii::t('app', 'E-Mail not found');
            }
        }

        return $this->render('recover', [
            'error' => $error,
            'confirm' => $confirm
        ]);
    }

    public function actionPassReset()
    {
        //Find the identification data
        if(Yii::$app->request->isGet) {
            $hash = Yii::$app->request->get('recover_hash');
            $id = Yii::$app->request->get('id');
        } elseif (Yii::$app->request->isPost){
            $temp_s = Yii::$app->request->post('Staff');
            $hash = $temp_s['recover_hash'];
            $id = $temp_s['id'];
        }

        if (!empty($hash) && !empty($id)) {
            //Verify identification data is valid
            $date = new \DateTime();
            $date->sub(new \DateInterval('PT24H'));

            $user = Staff::find()
                ->where(['id' => $id])
                ->andWhere(['recover_hash' => $hash])
                ->andWhere(['>','last_recover', $date->format('Y-m-d H:i')])
                ->one();
            if(empty($user)){
                throw new NotFoundHttpException(Yii::t('app',"User not found or no password reset request issued"));
            }

            $confirm = false;
            if(Yii::$app->request->isPost) {//Set a new password and save
                $user->load($temp_s);

                //Reset the recover data, so this link can be use only one time
                $user->recover_hash = '';
                $user->last_recover = '';
                if(!$user->save()){
                    throw new ErrorException($user->getErrors());
                }
                $confirm = true;
            }

            //Show the reset form, or just a confirm message
            return $this->render('pass-reset', [
                'staff' => $user,
                'confirm' => $confirm
            ]);
        } else {
            throw new BadRequestHttpException(Yii::t('app',"You didn't provide enough information for a password
            reset"));
        }
    }

    public function actionAgenda()
    {
        //date format string:
        $format = "Y-m-d";
        //retrieve all simulator data
        $simulators = Simulator::find()->all();

        //set navigator values;
        $day = \Yii::$app->request->get("day");
        if (empty($day) || !strtotime($day)) {
            // If the week is not set (properly), it's the today's one
            $day = date($format);
        }
        // Current, next and previous days of navigation
        $currDay = new DateTime($day);
        $nextDay = clone $currDay;
        $nextDay->modify("next day");
        $prevDay = clone $currDay;
        $prevDay->modify("previous day");

        $dayStarting = DateTime::createFromFormat("Y-m-d H:i:s", $currDay->format("Y-m-d") . " " . "00:00:00");
        $dayEnding = DateTime::createFromFormat("Y-m-d H:i:s", $currDay->format("Y-m-d") . " " . "23:59:59");

        // Find timeslots, bookings and related staff information
        $sim_slots = array();
        $bookings = array();
        $staff = array();
        //TODO to self: examine this code if the assigned instructor is moved to another table
        foreach ($simulators as $sim) {
            //foreach simulator find time slots with the given date
            $slots = Timeslot::find()->
            where(['id_simulator' => $sim->id])->
            andWhere(['>=', 'start', $dayStarting->format("c")])->
            andWhere(['<=', 'end', $dayEnding->format("c")])->all();
            $sim_slots[$sim->id] = $slots;
            foreach ($slots as $slot) {
                //foreach timeslot is found, get the booking information
                if ($slot->id_booking != null && !array_key_exists($slot->id_booking, $bookings)) {
                    $booking = Booking::findOne($slot->id_booking);
                    $bookings[$slot->id_booking] = $booking;
                    if ($booking->assigned_instructor != null && !array_key_exists($booking->assigned_instructor,
                            $staff)
                    ) {
                        //if the booking is assigned get the instructor details
                        $instructor = Staff::findOne($booking->assigned_instructor);
                        $staff[$instructor->id] = $instructor;
                    }
                }
            }
        }
        return $this->render("agenda", [
            'simulators' => $simulators,
            'currDay' => $currDay,
            'nextDay' => $nextDay->format($format),
            'prevDay' => $prevDay->format($format),
            'slots' => $sim_slots,
            'bookings' => $bookings,
            'staff' => $staff
        ]);
    }

    /**
     * Logout from the system
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->goBack();
    }

    /**
     * Shows a list of all the users in the system
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Staff::find(),
        ]);

        return $this->render('index', [
            'users' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Staff.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = Staff::findOne($id);

        return $this->render('view', [
            'model' => $model,
            'roles' => Yii::$app->authManager->getRolesByUser($model->id)
        ]);
    }

    /**
     * Creates a new Staff model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Staff();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if (Yii::$app->user->can('assignRoles')) {
                $this->updateRoles($model, Yii::$app->request->post('roles', []));
            }

            return $this->redirect(['view', 'id' => $model->id]);

        } else {
            return $this->render('create', [
                'model' => $model,
                'allRoles' => Yii::$app->authManager->getRoles()
            ]);
        }
    }


    /**
     * Shows a form to edit the informations of a user
     * @param integer $id the id of the user to edit
     * @return string
     */
    public function actionUpdate($id)
    {
        $s = Staff::findOne((int)$id);

        $loggedInUser = Yii::$app->user;

        /**
         * Unfortunately access control must be perfomed here and not in the AccessControl Filter
         * Because we need to pass the user param to the updateOwnProfile permission
         */
        if ($loggedInUser->can('manageStaff') || $loggedInUser->can('updateOwnProfile', ['user' => $s])) {

            if (empty($s)) {
                throw new NotFoundHttpException(Yii::t('app', "The specified user doesn't exist"));
            }

            if (Yii::$app->request->getIsPost()) {
                $s->load(Yii::$app->request->post());
                if ($s->save()) {//If basic save is successfull, go on with permissions save
                    if (Yii::$app->user->can('assignRoles')) {
                        $this->updateRoles($s, Yii::$app->request->post('roles', []));
                        return $this->redirect(['view', 'id' => $s->id]);
                    }
                }
            }
            else {
                return $this->render('update', [
                    'user' => $s,
                    'allRoles' => Yii::$app->authManager->getRoles(),
                    'roles' => Yii::$app->authManager->getRolesByUser($s->id)
                ]);
            }
        } else {
            //Not permission to access. If user is guest redirect to login, otherwise forbid
            if ($loggedInUser->isGuest) {
                return Yii::$app->user->loginRequired();
            } else {
                throw new ForbiddenHttpException(Yii::t('app', "You are not allowed to perform this action."));
            }
        }
    }

    /**
     * Updates the roles assigned to a user basing on the input from the POST
     * @param Staff $user the user to update
     * @param array $roles an array of roles to add. It should be indexed with the names of the roles
     */
    private function updateRoles($user, $roles)
    {
        $oldRoles = Yii::$app->authManager->getRolesByUser($user->id);

        $toDelete = array_diff_key($oldRoles, $roles);
        $toAdd = array_diff_key($roles, $oldRoles);

        foreach ($toDelete as $roleName => $rObj) {
            Yii::$app->authManager->revoke($rObj, $user->id);
        }

        foreach ($toAdd as $roleName => $value) {
            $r = Yii::$app->authManager->getRole($roleName);
            Yii::$app->authManager->assign($r, $user->id);
        }
    }


    private function sendRecovery(Staff $staff)
    {
        $now = new \DateTime();
        $staff->last_recover= $now->format('Y-m-d H:i');
        $staff->recover_hash = Yii::$app->getSecurity()->generateRandomString();
        $staff->save();

        Yii::$app->mailer->compose(['html' => 'staff/recovery_html', 'text' => 'staff/recovery_text'], [
            'staff' => $staff
        ])
            ->setFrom(Parameter::getValue('adminEmail',''))
            ->setTo($staff->email)
            ->setSubject(Yii::t('app', 'Booking System Password Recovery'))
            ->send();
    }
}