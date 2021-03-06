<?php

namespace app\controllers;

use app\models\Parameter;
use app\models\Simulator;
use app\models\Staff;
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
use yii\web\ForbiddenHttpException;
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
                'only' => ['index', 'update'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'update'],
                        'roles' => ['manageBookings']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['accept'],
                        'roles' => ['confirmBooking']
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
            'query' => Booking::find()->orderBy('timestamp DESC'),
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

        if ($model->load(Yii::$app->request->post())) {
            //instructor id comes as a string, convert it to int
            $ins = Yii::$app->request->post()['Booking']['assigned_instructor'];
            $ins_id = null;
            if ($ins != null) {
                $ins_id = (int)$ins;
            }
            $model->assigned_instructor = $ins_id;
            if ($model->save()) {
                return $this->redirect(['booking/view', 'id' => $id]);
            }
        }
        $me = Staff::findOne(\Yii::$app->user->id);
        $instructors = array();
        $staff = Staff::find()->all();
        foreach ($staff as $s) {
            if (\Yii::$app->authManager->checkAccess($s->id, 'assignedToBooking')) {
                $instructors[$s->id] = $s->name . ' ' . $s->surname;
            }
        }
        return $this->render('update', [
            'model' => $model,
            'me' => $me,
            'instructors' => $instructors
        ]);

    }

    /**
     * Deletes an existing Booking model.
     * The token of the booking is required to delete the booking
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $token = null)
    {
        $model = $this->findModel($id);
        if (Yii::$app->user->can('manageBookings')) {
            $token = $model->token;
        }
        if ($model->token != $token) {
            throw new ForbiddenHttpException(Yii::t('app', "You don't have permission to see this booking"));
        }

        try {
            Timeslot::handleDeleteBooking($model);
            $model->delete();

            // if the user who deleted the booking can manageBookings, send him to the booking/index, otherwise it is a customer, send him to the site/index
            if (Yii::$app->user->can('manageBookings')) {
                return $this->redirect(['index']);
            } else {
                return $this->redirect(['site/index']);
            }
        } catch(\ErrorException $e){
            throw new ServerErrorHttpException(Yii::t('app',"There was an unexpected error during the deletion of the booking."));
        }
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

            if (!$model->load((Yii::$app->request->post()))) {
                throw new BadRequestHttpException();
            }

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
        if (Yii::$app->user->can('manageBookings')) {
            if ($booking->assigned_instructor != null) {
                $instructor = Staff::findOne($booking->assigned_instructor);
                $booking->assigned_instructor_name = $instructor->name . ' ' . $instructor->surname;
            } else {
                $booking->assigned_instructor_name = Yii::t('app', 'Not assigned');
            }
            return $this->render('viewForStaff', [
                'model' => $booking,
                'entry_fee' => Parameter::getValue('entryFee', 80)
            ]);
        } else {
            $token = Yii::$app->request->get('token');
        }

        if ($booking->token != $token) {
            throw new ForbiddenHttpException(Yii::t('app', "You don't have permission to see this booking"));
        }

        //render the view page for anonymous user
        return $this->render('view', [
            'model' => $booking,
            'flight_price' => $this->sumTimeslotsCost($booking->timeslots),
            'entry_fee' => Parameter::getValue('entryFee', 80)
        ]);
    }

    /**
     * Action used when a coordinator confirms a booking
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionAccept($id)
    {
        $booking = $this->findModel($id);
        $booking->status = Booking::CONFIRMED;
        if (!$booking->save()) {
            throw new ServerErrorHttpException(Yii::t('app','There was an error while confirming the booking'));
        }
        return $this->redirect(['booking/view','id'=>$id]);
    }

    /**
     * Display booking and timeslots present in session variable
     * @return string
     */
    public function actionSummarizeBooking()
    {
        $booking = Yii::$app->session[self::SESSION_PARAMETER_BOOKING];
        $timeslots = Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT];

        return $this->render('summarize', [
            'model' => $booking,
            'timeSlots' => $timeslots,
            'flight_price' => $this->sumTimeslotsCost($timeslots),
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
        if (Yii::$app->request->get(self::GET_PARAMETER_TIME_SLOTS)) {
            $timeSlotIDs = Yii::$app->request->get(self::GET_PARAMETER_TIME_SLOTS);
            // Check whether time slot IDs are numeric and valid
            foreach ($timeSlotIDs as $timeSlotID) {
                if (!is_numeric($timeSlotID) or ((int)$timeSlotID) != $timeSlotID or $timeSlotID <= 0) {
                    throw new BadRequestHttpException(Yii::t('app', "You have specified invalid time slots"));
                }
            }

            // Retrieve time slots from the database with the given IDs
            $timeSlots = Timeslot::findAll($timeSlotIDs);

            // Keep the timeslots ordered chronologically
            usort($timeSlots, array($this, 'compTimeslots'));

            // Save time slots to the session
            $this->saveTimeSlotsToSession($timeSlots);
        }

        // Retrieve time slots from current session
        $sessionTimeSlots = Yii::$app->session->get(self::SESSION_PARAMETER_TIME_SLOT);

        if (empty($sessionTimeSlots)) {
            throw new BadRequestHttpException(Yii::t('app', "You must choose at least one time slot"));
        }

        // Populate model with data from the POST-Request
        $booking = new Booking();
        $wasPopulatedSuccessfully = $booking->load(Yii::$app->request->post());

        if ($wasPopulatedSuccessfully) {
            //a booking in opening hours is automatically confirmed
            $booking->status = Booking::CONFIRMED;
            //instructor id comes as a string, convert it to int
            if (array_key_exists('assigned_instructor', Yii::$app->request->post()['Booking'])) {
                $ins = Yii::$app->request->post()['Booking']['assigned_instructor'];
                $ins_id = null;
                if ($ins != null) {
                    $ins_id = (int)$ins;
                }
                $booking->assigned_instructor = $ins_id;
            }
            Yii::$app->session[self::SESSION_PARAMETER_BOOKING] = $booking;
            Yii::$app->session[self::SESSION_PARAMETER_WEEKDAYS] = false;

            // Summarize booking information
            return $this->actionSummarizeBooking();
        } else {
            $me = Staff::findOne(\Yii::$app->user->id);
            $instructors = array();
            $staff = Staff::find()->all();

            foreach ($staff as $s) {
                if (\Yii::$app->authManager->checkAccess($s->id, 'assignedToBooking')) {
                    $instructors[$s->id] = $s->name . ' ' . $s->surname;
                }
            }

            // Get the last timeslot
            $lastTimeslot = $sessionTimeSlots[count($sessionTimeSlots)-1];
            // and its next contiguous
            $nextTimeslot = $lastTimeslot->nextTimeslot();

            return $this->render('create', [
                'model' => $booking,
                'timeslots' => $sessionTimeSlots,
                'nextTimeslot' => $nextTimeslot,
                'flight_price' => $this->sumTimeslotsCost($sessionTimeSlots),
                'entry_fee' => Parameter::getValue('entryFee', 80),
                'me' => $me,
                'instructors' => $instructors
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
        $model = new Booking();

        $ok=true;
        $tmpSlot=[];
        $timeSlotError='';
        if ($model->load(Yii::$app->request->post())) {
            $simId = Yii::$app->request->post('simulator');
            if (empty($simId) or !is_numeric($simId)) {
                throw new BadRequestHttpException(Yii::t('app', 'You must specify a valid simulator'));
            }
            $s = Simulator::findOne($simId);
            if (empty($s)) {
                throw new NotFoundHttpException(Yii::t('app', 'The specified simulator doesn\'t exist'));
            }

            //a booking in non opening hours has to be confirmed
            $model->status = Booking::WAITING_FOR_CONFIRMATION;
            Yii::$app->session[self::SESSION_PARAMETER_BOOKING] = $model;
            Yii::$app->session[self::SESSION_PARAMETER_WEEKDAYS] = true;

            try {
                $tmpSlot = $this->timespanHandling(Yii::$app->request->post('Timeslot'), $s);
                if(count($tmpSlot)<=0){
                    $ok=false;
                    $timeSlotError = Yii::t('app', "You must specify at least one time span to book");
                }
            } catch(Exception $e){
                $ok=false;
                $timeSlotError = $e->getMessage();
            }

            if($ok) {
                Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT] = $tmpSlot;
            }
        }

        if ($ok and Yii::$app->request->isPost) {
            return $this->actionSummarizeBooking();
        } else{
            //New Booking, reset session to avoid merging data from previous unended bookings
            unset(Yii::$app->session[self::SESSION_PARAMETER_BOOKING]);
            unset(Yii::$app->session[self::SESSION_PARAMETER_WEEKDAYS]);
            unset(Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT]);

            $simId = Yii::$app->request->get('simulator');
            if (empty($simId) or !is_numeric($simId)) {
                throw new BadRequestHttpException(Yii::t('app', 'You must specify a valid simulator'));
            }
            $s = Simulator::findOne($simId);
            if (empty($s)) {
                throw new NotFoundHttpException(Yii::t('app', 'The specified simulator doesn\'t exist'));
            }

            try {
                $tmpSlot = $this->timespanHandling(Yii::$app->request->get('Timeslot'), $s);
            } catch(Exception $e){
                $timeSlotError = $e->getMessage();
            }

            $me = Staff::findOne(\Yii::$app->user->id);
            $instructors = array();
            $staff = Staff::find()->all();

            foreach ($staff as $user) {
                if (\Yii::$app->authManager->checkAccess($user->id, 'assignedToBooking')) {
                    $instructors[$user->id] = $user->name . ' ' . $user->surname;
                }
            }

            return $this->render('create-weekdays', [
                'error' => $timeSlotError,
                'model' => $model,
                'simulator' => $s,
                'timeslots' => empty($tmpSlot) ? [new Timeslot()] : $tmpSlot,
                'entry_fee' => Parameter::getValue('entryFee', 80),
                'instructors' => $instructors,
                'me' => $me,
                'businessHours' => [
                    'start' => Parameter::getValue('businessTimeStart'),
                    'end' => Parameter::getValue('businessTimeEnd')
                ]
            ]);
        }
    }

    private function timespanHandling($input, $simulator){
        $tmpSlot=[];
        $today= new \DateTime();

        if($input==null){
            return $tmpSlot;
        }

        foreach($input as $borders) {
            if (!empty($borders['start'])) {
                try {
                    $startDate = new \DateTime($borders['start']);
                    if (!empty($borders['end'])) {
                        $endDate = new \DateTime($borders['end']);
                    } else {
                        $endDate = clone $startDate;
                        $endDate->add(new \DateInterval("PT" . $simulator->flight_duration . "M"));
                    }
                } catch (Exception $e) {
                    throw new Exception(Yii::t('app', "You specified an invalid time span"));
                }

                if ($startDate <= $today) {
                    throw new Exception(Yii::t('app', "The specified time spans cannot be in the past"));
                }

                if ($endDate <= $startDate) {
                    throw new Exception(Yii::t('app',
                        "The ending date of all the time spans must be after its starting time"));
                }

                $slot = new Timeslot();
                $slot->start = $startDate->format('Y-m-d H:i');
                $slot->end = $endDate->format('Y-m-d H:i');
                $slot->id_simulator = $simulator->id;
                $slot->creation_mode = Timeslot::WEEKDAYS;

                $tmpSlot[] = $slot;
            }
        }

        return $tmpSlot;
    }

    /**
     * Save the booking in the current session and update timeslots booked.
     * Requires the presence of a booking and one or more time slot in the session variable
     *
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function actionConfirm()
    {
        if (!isset(Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT]) || !isset(Yii::$app->session[self::SESSION_PARAMETER_BOOKING])) {
            //anyway unset session to be sure (one of the three could be set)
            unset(Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT]);
            unset(Yii::$app->session[self::SESSION_PARAMETER_BOOKING]);
            unset(Yii::$app->session[self::SESSION_PARAMETER_WEEKDAYS]);

            throw new BadRequestHttpException(Yii::t('app', "You must choose at least one time slot"));
        }

        $transaction = Yii::$app->db->beginTransaction(Transaction::SERIALIZABLE);
        try {
            $timeSlots = Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT];
            $booking = Yii::$app->session[self::SESSION_PARAMETER_BOOKING];

            if (!$booking->save()) {
                throw new ErrorException();
            }

            foreach ($timeSlots as $slot) {
                $slot->id_booking = $booking->id;
                if (!$slot->save()) {
                    throw new ErrorException();
                }
            }

            if (Yii::$app->session[self::SESSION_PARAMETER_WEEKDAYS]) {
                $this->notifyCoordinators($booking);
            }

            $this->notifyCustomer($booking, $timeSlots);

            unset(Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT]);
            unset(Yii::$app->session[self::SESSION_PARAMETER_BOOKING]);
            unset(Yii::$app->session[self::SESSION_PARAMETER_WEEKDAYS]);

            $transaction->commit();
            return $this->redirect(['view', 'id' => $booking->id, 'token' => $booking->token]);
        } catch (ErrorException $e) {
            $transaction->rollBack();
            unset(Yii::$app->session[self::SESSION_PARAMETER_TIME_SLOT]);
            unset(Yii::$app->session[self::SESSION_PARAMETER_BOOKING]);
            unset(Yii::$app->session[self::SESSION_PARAMETER_WEEKDAYS]);

            throw new BadRequestHttpException((Yii::t('app', "There was an error while saving your booking to the system. We apologize for the error, please try again later.")));
        }
    }

    /**
     * Receives an array of time slots and saves it to the current session. Note that the given array can also be empty though.
     *
     * @param $timeSlots
     */
    private function saveTimeSlotsToSession($timeSlots)
    {
        if ($timeSlots == null) {
            return;
        }

        $sessionTimeSlots = [];

        // Retrieve time slots
        foreach ($timeSlots as $timeSlot) {
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
    private function isValidTimeSlot($timeSlot)
    {
        $isValid = true;

        if (empty($timeSlot)) {
            $isValid = false;
        }

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
     * Calculates the total cost of multiple Timeslots
     * NOTE: it doesn't include entry fees or any other fee unrelated to the cost of the simulation
     * @param Timeslot[] $timeslots
     * @return int total cost of the simulations
     */
    private function sumTimeslotsCost($timeslots)
    {
        $simulationFee = 0;

        foreach ($timeslots as $timeslot) {
            // Add to the price of the simulation
            $simulationFee += $timeslot->calculateCost();
        }

        return $simulationFee;
    }


    /**
     * A comparison function for timeslots to be used with usort
     * @param Timeslot $a
     * @param Timeslot $b
     * @return int less than or greater than zero if the starting time of the first timeslot respectively precedes
     * or succeeds the second timeslot's one
     */
    private function compTimeslots($a, $b)
    {
        return strtotime($a->start) - strtotime($b->start);
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
            throw new NotFoundHttpException(Yii::t('app', "The requested booking was not found"));
        }
    }

    /**
     * Return the model that match with the model in input
     * @param $model_input should contain at least id, name and surname
     * @throws NotFoundHttpException
     */
    protected function findModelForSearch($model_input)
    {
        if (Yii::$app->user->can('manageBookings')) {
            $query = Booking::find()
                ->where([
                    'name' => $model_input->name,
                    'surname' => $model_input->surname,
                ]);
        } else {
            $query = Booking::find()
                ->where([
                    'name' => $model_input->name,
                    'surname' => $model_input->surname,
                    'token' => $model_input->token
                ]);
        }
        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', "The requested booking was not found"));
        }
    }


    private function notifyCoordinators($booking)
    {
        Yii::$app->mailer->compose(['html' => 'booking/new_booking_html', 'text' => 'booking/new_booking_text'], [
            'id' => $booking->id,
            'mailText' => Parameter::getValue('emailTextToCoordinator')
        ])
            ->setFrom(\Yii::$app->params['adminEmail'])
            ->setTo(Parameter::getValue('coordinatorEmail'))
            ->setSubject(\Yii::t('app', 'New Booking'))
            ->send();
    }

    private function notifyCustomer($booking, $timeslots)
    {
        if ($booking->email != null) {
            Yii::$app->mailer->compose([
                'html' => 'booking/customer_booking_html',
                'text' => 'booking/customer_booking_text'
            ], [
                'booking' => $booking,
                'totalSimulationCost' => $this->sumTimeslotsCost($timeslots),
                'entryFee' => Parameter::getValue('entryFee', 80),
                'timeslots' => $timeslots,
            ])
                ->setFrom(\Yii::$app->params['adminEmail'])
                ->setTo($booking->email)
                ->setSubject(\Yii::t('app', 'Your Booking'))
                ->send();
        }
    }
}
