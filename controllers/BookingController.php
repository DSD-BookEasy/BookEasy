<?php

namespace app\controllers;

use app\models\Parameter;
use app\models\Timeslot;
use Faker\Provider\DateTime;
use Yii;
use app\models\Booking;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\Transaction;
use yii\web\BadRequestHttpException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * BookingController implements the CRUD actions for Booking model.
 */
class BookingController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update'],
                'rules' => [
                    [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['@']
                    ],
                ],

            ],
        ];
    }

    /**
     * The action allow the possibility to search for a specific booking.
     * The id of the booking to search is insert by a HTML form, using the YII support of form
     */
    public function actionSearch()
    {

        $model = new Booking();
        $model->scenario = 'search';
        if ((Yii::$app->request->post())) {

            if(!$model->load((Yii::$app->request->post()))){
                throw new \ErrorException();
            }

            //this line solve a bug! Don't delete it!--> bug solved?? wait more to delete these 2 lines
            //$model->id = Yii::$app->request->post('Booking')['id']; //I don't know why but the load doesn't load the id
            //$model->token = Yii::$app->request->post('Booking')['token'];

            $model = $this->findModelForSearch($model);

            return $this->redirect([
                'view',
                'id' => $model->id,
                'token' => $model->token
            ]);
        } else {
            return $this->render('search', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Lists all Booking models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Booking::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Booking model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $booking = $this->findModel($id);
        if (Yii::$app->user->getId() != null) {
            $token = $booking->token;
        }else{
            $token = Yii::$app->request->get('token');

        }

        if(strcmp($booking->token, $token) != 0){
            //error page
            throw new \ErrorException();
        }

        return $this->render('view', [
            'model' => $booking,
            'entry_fee' => Parameter::getValue('entryFee', 80)
        ]);
    }

    /**
     * Creates a new Booking model for sunday or for day included in the timeSlotModel.
     * Expects an array of timeSlot ids in the POST "timeslot" field.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * The action work as a transaction: the booking and timeslots update are performed in an atomic transaction
     * @return mixed
     */
    public function actionCreate()
    {
        $timeSlotIDs = Yii::$app->request->get('timeslots');

        if ($timeSlotIDs == null) {

        }

        //Accept only an array of integer values
        foreach ($timeSlotIDs as $timeSlotID) {
            if (!is_numeric($timeSlotID) or ((int)$timeSlotID) != $timeSlotID or $timeSlotID <= 0) {
                throw new BadRequestHttpException("Invalid timeslots were specified");
            }
        }

        $sessionTimeSlots = Timeslot::findAll($timeSlotIDs);

        $this->saveTimeSlotsToSession($sessionTimeSlots);

        Yii::$app->session['timeslots'] = $sessionTimeSlots;

        if (empty(Yii::$app->session['timeslots'])) {
            throw new BadRequestHttpException("You must specify the timeslots to book");
        }

        $model = new Booking();

        if ($model->load(Yii::$app->request->post())) {
            //lock

            $transaction = Yii::$app->db->beginTransaction(Transaction::SERIALIZABLE);
            try {
                $timeSlots = Yii::$app->session['timeslots'];

                if (!$model->save()) {//Note: does the framework automatically update the id on insert?
                    //rise error
                    throw new \ErrorException('jjfjfg');
                }


                foreach ($timeSlots as $slot) {
                    //rise error for problems on save or if booking is not available
                    if (!$slot->isBooked()){
                        $slot->id_booking = $model->id;
                        if(!$slot->save()){
                            throw new ErrorException($slot->id);
                        }
                    }else{
                        throw new ErrorException($slot->id);
                    }
                }

                // Unset session, otherwise it will stay hanging forever
                unset(Yii::$app->session['timeslots']);

                $transaction->commit();
                $this->notifyCoordinators($model);
                // Fix exception
                return $this->redirect(['view', 'id' => $model->id, 'token' => $model->token]);
            } catch (Exception $e) {
                $transaction->rollBack();
                //TODO here we should go to error page
                throw new ServerErrorHttpException("Saving your booking failed");
            }

        } else {
            return $this->render('create', [
                'model' => $model,
                'timeslots' => Yii::$app->session['timeslots'],
                'entry_fee' => Parameter::getValue('entryFee', 80)
            ]);
        }
    }

    /**
     * Creates a new Booking model for weekdays and also timeslots, passed in post, are insert in the database.
     * Expects to receive Timeslots in the GET "timeslots" field, as in the standard Yii format (as an array represeting
     * fields of the Timeslot model).
     * If creation is successful, the browser will be redirected to the 'view' page.
     * The action works as a transaction: the booking and timeslots insert are performed in an atomic transaction.
     * @return mixed
     */
    public function actionCreateWeekdays()
    {
        // INFO: We could use a constant variable for the session parameter here

        // This is an temporary GET-array of time slots
        $tmpTimeSlots = Yii::$app->request->get('timeslots');

        $timeSlots = [];
        foreach($tmpTimeSlots as $key => $value) {
            $timeSlot = new Timeslot();
            $timeSlot->load($value, '');

            $timeSlots[] = $timeSlot;
        }

        $this->saveTimeSlotsToSession($timeSlots);

        $sessionTimeSlots = Yii::$app->session['timeslots'];

        if (empty($sessionTimeSlots)) {
            throw new BadRequestHttpException("Invalid selection of time slots");
        }

        $model = new Booking();
        $model->scenario = 'weekdays';
        if ($model->load(Yii::$app->request->post())) {
            //lock
            $transaction = Yii::$app->db->beginTransaction(Transaction::SERIALIZABLE);
            try {
                $timeSlots = $sessionTimeSlots;

                if (!$model->save()) {
                    //rise error
                    throw new ErrorException();
                }

                foreach ($timeSlots as $slot) {
                    $slot->id_booking = $model->id;
                    $slot->creation_mode = Timeslot::WEEKDAYS;
                    if (!$slot->save()) {
                        //rise error
                        throw new ErrorException();
                    }
                }

                $transaction->commit();
                unset(Yii::$app->session['timeslots']);
                $this->notifyCoordinators($model);
                return $this->redirect(['view', 'id' => $model->id, 'token' => $model->token]);
            } catch (ErrorException $e) {
                $transaction->rollBack();
                unset(Yii::$app->session['timeslots']);
                throw new BadRequestHttpException();
            }
        } else {
            return $this->render('createWeekdays', [
                'model' => $model,
                'timeslots' => Yii::$app->session['timeslots'],
                'entry_fee' => Parameter::getValue('entryFee', 80)
            ]);
        }
    }

    /**
     * Receives an array of time slots and saves it to the current session. Note that the given array can also be empty though.
     *
     * @param $timeSlots
     */
    private function saveTimeSlotsToSession($timeSlots) {
        if ($timeSlots == null) {
            return;
        }

        $sessionTimeSlots = [];

        // Retrieve time slots
        foreach($timeSlots as $timeSlot) {
            if ($this->isValidTimeSlot($timeSlot) == false) {
                continue;
            }

            $sessionTimeSlots[] = $timeSlot;
        }

        // Note that sessionTimeSlots can also be empty
        Yii::$app->session['timeslots'] = $sessionTimeSlots;
    }

    /**
     * Checks whether a time slot is valid based on its ID, start time and end time.
     *
     * @param $timeSlot
     * @return bool
     */
    private function isValidTimeSlot ($timeSlot) {
        $isValid = true;

        if (empty($timeSlot)) {
            $isValid = false;
        }

        // Make sure we deal with a valid time slot
        if (!empty($timeSlot->id)) {
            $isValid = false;
        }

        // Make sure start time is before end time
        if ($timeSlot->start > $timeSlot->end) {
            $isValid = false;
        }

        // Make sure start time is the future
        $currentDate = date('Y-m-d');
        if ($timeSlot->start < $currentDate) {
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * Updates an existing Booking model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Booking model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model =  $this->findModel($id);
        Timeslot::handleDeleteBooking($model);
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Booking model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Booking the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Booking::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Return the model that match with the model in input
     * @param $model_input should contain at least id, name and surname
     * @throws NotFoundHttpException
     */
    protected function findModelForSearch($model_input)
    {
        $query = Booking::find()
            ->where(['name' => $model_input->name,
                    'surname' => $model_input->surname,
                    'token' => $model_input->token
                    ]);

        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Incorrect input');
        }
    }


    private function notifyCoordinators($booking)
    {
        Yii::$app->mailer->compose(['html' => 'booking/new_booking_html', 'text' => 'booking/new_booking_text'], [
            'id' => $booking->id
        ])
            ->setFrom(\Yii::$app->params['adminEmail'])
            ->setTo(\Yii::$app->params['coordinatorEmail'])
            ->setSubject(\Yii::t('app', 'New Booking'))
            ->send();
    }
}
