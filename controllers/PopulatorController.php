<?php

namespace app\controllers;

/**
 * define working hours and generated data range here
 * REMEMBER!: timeslot endings must coincide with midday!
 * REMEMBER!: Keep the below format if you change the dates YYYY-MM-DD HH:mm:ss
 **/
define ('_beginDay_H', '09');
define ('_beginDay_M', '00');
define('_midday_H', '12');
define('_midday_M', '00');
define('_endDay_H', '17');
define('_endDay_M', '59');
define('_interval', '+30 minutes');
define('_lunchBreak', '+2 hours');
define('_monday', '2014-12-08');
define('_sunday', '2014-12-14');
//testing purpose
use app\models\Booking;
use app\models\Simulator;
use app\models\Staff;
use app\models\Timeslot;
use app\models\TimeslotModel;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\db\Migration;
use yii\db\IntegrityException;

class PopulatorController extends \yii\web\Controller
{
    /**
     * @param $date Reference to date of next timeslot
     * @param $lunchBreak Amount of the time break for lunch
     */
    private function next(&$date, &$lunchBreak)
    {
        $midday = clone $date;
        date_time_set($midday, _midday_H, _midday_M);
        $endDay = clone $date;
        date_time_set($endDay, _endDay_H, _endDay_M);
        if ($date == $midday) {
            $date->add($lunchBreak);
        } else {
            if ($date > $endDay) {
                if (date('w', $date) == 6) {
                    $date->add(\DateInterval::createFromDateString('+39 hours'));
                } else {
                    $date->add(\DateInterval::createFromDateString('+15 hours'));
                }

            }
        }
    }

    /**
     * @param $objectName Name of the object which is being created
     * @param $element Array of the object which contains the properties
     * @return mixed New object created with the given parameters
     */
    private function map($objectName, $element)
    {
        $object = new $objectName;
        foreach ($element as $key => $val) {
            $object->{$key} = $val;
        }
        return $object;
    }

    public function actionIndex()
    {
        return $this->render('index', ['user' => new Staff()]);
    }

    public function actionClear()
    {
        Timeslot::deleteAll();
        Booking::deleteAll();
        $simulators = Simulator::find()->all();
        $tmpFolderPath = Yii::getAlias('@webroot') . '/uploads';
        // Check whether the folder in which we will temporary save the uploaded image exists
        if (!file_exists($tmpFolderPath)) {
            Yii::info("$tmpFolderPath doesn't exist. It will be created.");
            mkdir($tmpFolderPath);
        }
        foreach ($simulators as $simulator) {
            $simulator->clearImagesCache();
            $simulator->removeImages();
        }
        Simulator::deleteAll();
        Staff::deleteAll();
        TimeslotModel::deleteAll();
        Yii::$app->db->createCommand("truncate table " . Timeslot::tableName())->query();
        Yii::$app->db->createCommand("truncate table " . Booking::tableName())->query();
        Yii::$app->db->createCommand("truncate table " . Simulator::tableName())->query();
        Yii::$app->db->createCommand("truncate table " . Staff::tableName())->query();
        Yii::$app->db->createCommand("truncate table " . TimeslotModel::tableName())->query();
        Yii::$app->db->createCommand("truncate table image;")->query();
        Yii::$app->authManager->removeAllAssignments();
        //Yii::$app->authManager->removeAll();
        return $this->render('index', ['user' => new Staff()]);
    }

    public function actionExecute()
    {
        //loads and creates staff objects and then saves it to the db
        $staff = require(__DIR__ . '/../tests/codeception/fixtures/staff.php');
        $staff_ids = array();
        if (Yii::$app->request->getIsPost()) {
            $r = Yii::$app->authManager->getRole("Instructor");
            foreach ($staff as $ele) {
                $object = $this->map('app\models\Staff', $ele);
                $object->save();
                try {
                    Yii::$app->authManager->assign($r, $object->id);
                } catch (Exception $e) {

                }
                array_push($staff_ids, $object->id);
            }


            $user = new Staff();
            $user->load(Yii::$app->request->post());
            $user->email = 'mail@mail.com';
            $user->name = 'admin_name';
            $user->surname = 'admin_surname';
            if (!$user->save()) {
                throw new ErrorException('Admin user could not be created. There is a problem with the populator code, consult Mert');
            }
            $roles = Yii::$app->authManager->getRolesByUser($user->id);
            if (count($roles) == 0) {
                $r = Yii::$app->authManager->getRole("Admin");
                Yii::$app->authManager->assign($r, $user->id);
            }
            try {
                $r = Yii::$app->authManager->getRole('Instructor');
                $permission = Yii::$app->authManager->getPermission('manageBookings');
                Yii::$app->authManager->addChild($r, $permission);
                $permission = Yii::$app->authManager->getPermission('assignedToBooking');
                Yii::$app->authManager->addChild($r, $permission);
                $permission = Yii::$app->authManager->getPermission('assignInstructors');
                Yii::$app->authManager->addChild($r, $permission);
            } catch (IntegrityException $exp) {
                //roles are already assigned
            }
        } else {
            return $this->actionIndex();
        }

        //loads and creates bookings objects and then saves it to the db
        $bookings = require(__DIR__ . '/../tests/codeception/fixtures/booking.php');
        $assigned_bookings_ids = array();
        $unassigned_bookings_ids = array();
        foreach ($bookings as $ele) {
            $object = $this->map('app\models\Booking', $ele);
            if (rand(0, 1) > 0) { //randomly assign someone or leave it unassigned
                $object->{'assigned_instructor'} = array_values($staff_ids)[rand(0, count($staff_ids) - 1)];
                $object->save();
                array_push($assigned_bookings_ids, $object->id);
            } else {
                $object->save();
                array_push($unassigned_bookings_ids, $object->id);
            }
        }
        //mix the order
        shuffle($assigned_bookings_ids);
        shuffle($unassigned_bookings_ids);

        //loads and creates simulator objects and then saves it to the db
        $tmpFolderPath = Yii::getAlias('@webroot') . '/uploads';
        // Check whether the folder in which we will temporary save the uploaded image exists
        if (!file_exists($tmpFolderPath)) {
            Yii::info("$tmpFolderPath doesn't exist. It will be created.");
            mkdir($tmpFolderPath);
        }
        $simulators = require(__DIR__ . '/../tests/codeception/fixtures/simulator.php');
        $simulators_ids = array();
        foreach ($simulators as $ele) {
            $object = $this->map('app\models\Simulator', $ele);
            $result = \Faker\Provider\Image::image($dir = $tmpFolderPath,
                $width = 250, $height = 250, $category = "abstract", $fullPath = true);
            $object->save();
            $object->clearImagesCache();
            $object->attachImage($result, true);
            unlink($result);
            array_push($simulators_ids, $object->id);
        }
        //This section can be changed to generate time slots in different dates
        $format_string = "Y-m-d H:i:s";
        $format_string_time = "H:i:s";
        $sunday = \DateTime::createFromFormat($format_string, _sunday . ' ' . _beginDay_H . ':' . _beginDay_M . ':00');
        $midday = \DateTime::createFromFormat($format_string, _sunday . ' ' . _midday_H . ':' . _midday_M . ':00');
        $weekday = \DateTime::createFromFormat($format_string, _monday . ' ' . _beginDay_H . ':' . _beginDay_M . ':00');
        $interval = \DateInterval::createFromDateString(_interval);
        $lunchBreak = \DateInterval::createFromDateString(_lunchBreak);
        $endDay = \DateTime::createFromFormat($format_string, _sunday . ' ' . _endDay_H . ':' . _endDay_M . ':00');


        //1) sunday bookings
        //1.1) already reserved and assigned
        while (count($assigned_bookings_ids) > 0 and $sunday < $endDay) {
            $val = array_pop($assigned_bookings_ids);
            $time_slot = new Timeslot();
            if ($sunday == $midday) {
                $sunday->add($lunchBreak);
            }
            $time_slot->start = $sunday->format($format_string);
            $time_slot->end = $sunday->add($interval)->format($format_string);
            $time_slot->cost = rand(100, 999);
            $time_slot->id_simulator = array_values($simulators_ids)[rand(0, count($simulators_ids) - 1)];
            $time_slot->id_booking = $val;
            $time_slot->save();
        }
        //1.2) not reserved empty timeslots
        if ($sunday < $endDay) {
            if ($sunday == $midday) {
                $sunday->add($lunchBreak);
            }
            $time_slot = new Timeslot();
            $time_slot->start = $sunday->format($format_string);
            $time_slot->end = $sunday->add($interval)->format($format_string);
            $time_slot->cost = rand(100, 999);
            $time_slot->id_simulator = array_values($simulators_ids)[rand(0, count($simulators_ids) - 1)];
            $time_slot->save();
        }
        if ($sunday < $endDay) {
            if ($sunday == $midday) {
                $sunday->add($lunchBreak);
            }
            $time_slot = new Timeslot();
            $time_slot->start = $sunday->format($format_string);
            $time_slot->end = $sunday->add($interval)->format($format_string);
            $time_slot->cost = rand(100, 999);
            $time_slot->id_simulator = array_values($simulators_ids)[rand(0, count($simulators_ids) - 1)];
            $time_slot->save();
        }
        //1.3) reserved but not assigned
        while (count($unassigned_bookings_ids) > 0 and $sunday < $endDay) {
            $ele = array_pop($unassigned_bookings_ids);
            $time_slot = new Timeslot();
            if ($sunday == $midday) {
                $sunday->add($lunchBreak);
            }
            $time_slot->start = $sunday->format($format_string);
            $time_slot->end = $sunday->add($interval)->format($format_string);
            $time_slot->cost = rand(100, 999);
            $time_slot->id_simulator = array_values($simulators_ids)[rand(0, count($simulators_ids) - 1)];
            $time_slot->id_booking = $ele;
            $time_slot->save();
        }

        //2) weekday bookings
        //2.1) assigned weekday bookings if any left
        while (count($assigned_bookings_ids) > 0) {
            $val = array_pop($assigned_bookings_ids);
            $time_slot = new Timeslot();
            $time_slot->start = $weekday->format($format_string);
            $time_slot->end = $weekday->add($interval)->format($format_string);
            $this->next($weekday, $lunchBreak);
            $time_slot->cost = rand(100, 999);
            $time_slot->id_simulator = array_values($simulators_ids)[rand(0, count($simulators_ids) - 1)];
            $time_slot->id_booking = $val;
            $time_slot->save();
        }
        //2.2) unassigned weekday bookings if any left
        while (count($unassigned_bookings_ids) > 0) {
            $ele = array_pop($unassigned_bookings_ids);
            $time_slot = new Timeslot();
            $time_slot->start = $weekday->format($format_string);
            $time_slot->end = $weekday->add($interval)->format($format_string);
            $this->next($weekday, $lunchBreak);
            $time_slot->cost = rand(100, 999);
            $time_slot->id_simulator = array_values($simulators_ids)[rand(0, count($simulators_ids) - 1)];
            $time_slot->id_booking = $ele;
            $time_slot->save();
        }

        //TimeslotModel for sundays:
        $tempDateEnd = \DateTime::createFromFormat("Y-m-d", _sunday);
        $tempDateEnd->add(\DateInterval::createFromDateString("+6 months"));
        $tempDateEnd = $tempDateEnd->format("Y-m-d");
        $tempDateStart = _sunday;
        $sunday = \DateTime::createFromFormat($format_string,
            _sunday . ' ' . _beginDay_H . ':' . _beginDay_M . ':00');
        while ($sunday < $endDay) {
            $isBlocking = false;
            if ($sunday == $midday) {
                $isBlocking = true;
            }
            foreach ($simulators_ids as $id) {
                $currentSlotTime = clone $sunday;
                $timeslotmodel = new TimeslotModel();
                $timeslotmodel->id_simulator = $id;
                $timeslotmodel->start_validity = $tempDateStart;
                $timeslotmodel->end_validity = $tempDateEnd;
                //$timeslotmodel->last_generation = null;
                //$timeslotmodel->generated_until = null;
                $timeslotmodel->repeat_day = 7;
                $timeslotmodel->frequency = "P1W";
                $timeslotmodel->start_time = $currentSlotTime->format($format_string_time);
                if ($isBlocking) {
                    $timeslotmodel->end_time = $currentSlotTime->add($lunchBreak)->format($format_string_time);
                } else {
                    $timeslotmodel->end_time = $currentSlotTime->add($interval)->format($format_string_time);
                }
                $timeslotmodel->blocking = $isBlocking;
                if (!$timeslotmodel->save()) {
                    throw new ErrorException("Couldn't save: sim id: " . $id . " start validity: " . $tempDateStart
                        . " end validity: " . $tempDateEnd . " start_time: " . $timeslotmodel->start_time . " end_time: " . $timeslotmodel->end_time);
                };
            }
            if ($isBlocking) {
                $sunday->add($lunchBreak);
            } else {
                $sunday->add($interval);
            }
        }
        TimeslotModel::generateNextTimeslot(\DateTime::createFromFormat("Y-m-d", "2015-04-01"));
        return $this->goHome();
    }

}
