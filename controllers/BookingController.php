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
    // GET parameter
    const GET_PARAMETER_TIME_SLOTS = "timeslots";

    // Session parameter
    const SESSION_PARAMETER_BOOKING = 'booking_data';
    const SESSION_PARAMETER_TIME_SLOT = 'timeslots';
    const SESSION_PARAMETER_WEEKDAYS = 'weekdays';

    // Error messages
    const ERROR_MESSAGE_NO_TIME_SLOTS = "You must choose at least one time slot";
    const ERROR_MESSAGE_INVALID_TIME_SLOTS = "You have chosen invalid time slots";

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
        $model = $this->findModel($id);
        Timeslot::handleDeleteBooking($model);
        $model->delete();
        return $this->redirect(['index']);
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
     * Display booking and timeslots present in session variable
     * @return string
     */
    public function actionSummarizeBooking(){
        return $this->render('summarize', [
            'model' => Yii::$app->session[self::SESSION_PARAMETER_BOOKING],
            'timeSlots' => Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT],
            'entry_fee' => Parameter::getValue('entryFee', 80)
        ]);
    }

    /**
     * Creates a new Booking model for sunday or for day included in the timeSlotModel.
     * Expects an array of timeSlot ids in the POST "timeslot" field.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * The action work as a transaction: the booking and timeslots update are performed in an atomic transaction
     *
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        // Check time slots in the GET-Request

        //this if solves is necessary... don't delete it ;)
        if(Yii::$app->request->get(self::GET_PARAMETER_TIME_SLOTS)){
            $timeSlotIDs = Yii::$app->request->get(self::GET_PARAMETER_TIME_SLOTS);
            // Check whether time slot IDs are numeric and valid
            foreach ($timeSlotIDs as $timeSlotID) {
                if (!is_numeric($timeSlotID) or ((int)$timeSlotID) != $timeSlotID or $timeSlotID <= 0) {
                    throw new BadRequestHttpException(self::ERROR_MESSAGE_INVALID_TIME_SLOTS);
                }
            }

            // Retrieve time slots from the database with the given IDs
            $timeSlots = Timeslot::findAll($timeSlotIDs);

            // Save time slots to the session
            $this->saveTimeSlotsToSession($timeSlots);
        }

        // Retrieve time slots from current sesscion
        $sessionTimeSlots = Yii::$app->session->get(self::SESSION_PARAMETER_TIME_SLOT);

        if (empty($sessionTimeSlots)) {
            throw new BadRequestHttpException(self::ERROR_MESSAGE_NO_TIME_SLOTS);
        }

        // Populate model with data from the POST-Request
        $booking = new Booking();
        $wasPopulatedSuccessfully = $booking->load(Yii::$app->request->post());

        if ($wasPopulatedSuccessfully) {
            //a booking in opening hours is automatically confirmed
            $booking->status = Booking::CONFIRMED;
            Yii::$app->session[self::SESSION_PARAMETER_BOOKING] = $booking;
            Yii::$app->session[self::SESSION_PARAMETER_WEEKDAYS] = false;

            // Summarize booking information
            return $this->actionSummarizeBooking();
        } else {
            return $this->render('create', [
                'model' => $booking,
                'timeslots' => $sessionTimeSlots,
                'entry_fee' => Parameter::getValue('entryFee', 80)
            ]);
        }
    }

    /**
     * Creates a new Booking model for weekdays and also time slots, passed in post, are insert in the database.
     * Expects to receive time slots in the GET "timeslots" field, as in the standard Yii format (as an array representing
     * fields of the time slot model).
     * If creation is successful, the browser will be redirected to the 'view' page.
     * The action works as a transaction: the booking and timeslots insert are performed in an atomic transaction.
     *
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionCreateWeekdays()
    {
        // Check time slot values in the GET-Request
        $tmpTimeSlots = Yii::$app->request->get(self::GET_PARAMETER_TIME_SLOTS);

        // Provide an empty array for time slot
        $timeSlots = [];

        // Create time slots for every GET-Parameter
        if (empty($tmpTimeSlots) == false) {
            foreach ($tmpTimeSlots as $key => $value) {
                $timeSlot = new Timeslot();
                $timeSlot->load($value, '');

                $timeSlots[] = $timeSlot;
            }
        }

        // Save time slots to session
        $this->saveTimeSlotsToSession($timeSlots);

        // Retrieve time slots from current session
        $sessionTimeSlots = Yii::$app->session->get(self::SESSION_PARAMETER_TIME_SLOT);

        if (empty($sessionTimeSlots)) {
            throw new BadRequestHttpException(self::ERROR_MESSAGE_NO_TIME_SLOTS);
        }

        $model = new Booking();
        $model->scenario = 'weekdays';

        if ($model->load(Yii::$app->request->post())) {
            //a booking in non opening hours has to be confirmed
            $model->status = Booking::NOT_CONFIRMED;
            Yii::$app->session[self::SESSION_PARAMETER_BOOKING] = $model;
            Yii::$app->session[self::SESSION_PARAMETER_WEEKDAYS] = true;
            return $this->actionSummarizeBooking();
        } else {
            return $this->render('createWeekdays', [
                'model' => $model,
                'timeslots' => $sessionTimeSlots,
                'entry_fee' => Parameter::getValue('entryFee', 80)
            ]);
        }
    }

    /**
     * Save the booking in the current session and update timeslots booked.
     * Requires the presence of a booking and one or more time slot in the session variable
     *
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function actionConfirm(){
        if(!isset(Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT]) || !isset(Yii::$app->session[self::SESSION_PARAMETER_BOOKING])){
            //anyway unset session to be sure (one of the two could be set)
            unset(Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT]);
            unset(Yii::$app->session[self::SESSION_PARAMETER_BOOKING]);

            throw new BadRequestHttpException();
        }

        $transaction = Yii::$app->db->beginTransaction(Transaction::SERIALIZABLE);
        try {
            $timeSlots = Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT];
            $booking = Yii::$app->session[self::SESSION_PARAMETER_BOOKING];

            if (!$booking->save()) {
                //rise error
                throw new ErrorException();
            }

            foreach ($timeSlots as $slot) {
                $slot->id_booking = $booking->id;
                $slot->creation_mode = Timeslot::WEEKDAYS;
                if (!$slot->save()) {
                    //rise error
                    throw new ErrorException();
                }
            }

            $transaction->commit();

            //is require also for the opening hours?
            $this->notifyCoordinators($booking);

            unset(Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT]);
            unset(Yii::$app->session[self::SESSION_PARAMETER_BOOKING]);
            return $this->redirect(['view', 'id' => $booking->id, 'token' => $booking->token]);
        } catch (ErrorException $e) {
            $transaction->rollBack();
            unset(Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT]);
            unset(Yii::$app->session[self::SESSION_PARAMETER_BOOKING]);

            throw new BadRequestHttpException();
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
        Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT] = $sessionTimeSlots;
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

        // NOTE: A time slots does not necessary needs to have an ID since they can also be chosen freely
        // Make sure we deal with a valid time slot
        //if (empty($timeSlot->id)) {
            // $isValid = false;
        //}

        // Make sure start time is before end time
        if (strtotime($timeSlot->start) > strtotime($timeSlot->end)) {
            $isValid = false;
        }

        // Make sure start time is the future
        $currentDate = time();
        if (strtotime($timeSlot->start) < $currentDate) {
            $isValid = false;
        }

        return $isValid;
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
