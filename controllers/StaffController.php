<?php

namespace app\controllers;

use app\models\Booking;
use app\models\Simulator;
use app\models\Staff;
use app\models\Timeslot;
use DateTime;
use yii\filters\AccessControl;

class StaffController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [//Allow access to logout only if user is logged-in
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
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
        //Already loggedin users should not access this page
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $loginData = \Yii::$app->request->post('Staff');
        //No data sent, show the form, link the controller with the view
        if (empty($loginData)) {
            return $this->render('login', [
                'model' => new Staff(),
            ]);
        } else {
            $staff = Staff::findOne(['user_name' => $loginData['user_name']]);
            if (!empty($staff) and $staff->isValidPassword($loginData['password'])) {
                \Yii::$app->user->login($staff, 3600 * 24 * 30);
                return $this->goHome();
            } else {
                $staff = new Staff();
                $staff->user_name = $loginData['user_name'];
                return $this->render('login', [
                    'model' => $staff,
                    'error' => \Yii::t('app', 'Invalid Username or Password')
                ]);
            }
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

        // Find timeslots and the bookings in the current day
        $sim_slots = array();
        $bookings = array();
        foreach($simulators as $sim) {
            $slots = Timeslot::find()->
            where(['id_simulator' => $sim->id])->
            andWhere(['>=', 'start', $dayStarting->format("c")])->
            andWhere(['<=', 'end', $dayEnding->format("c")])->all();
            $sim_slots[$sim->id] = $slots;
            foreach($slots as $slot) {
                if ($slot->id_booking != null) {
                    $booking = Booking::findOne($slot->id_booking);
                    $bookings[$slot->id_booking] = $booking;
                }
            }
        }

        return $this->render("agenda", [
            'simulators' => $simulators,
            'currDay' => $currDay,
            'nextDay' => $nextDay->format($format),
            'prevDay' => $prevDay->format($format),
            'slots' => $sim_slots,
            'bookings' => $bookings
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
}
