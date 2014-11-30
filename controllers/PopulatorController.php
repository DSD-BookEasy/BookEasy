<?php

namespace app\controllers;

/**
 * define working hours and generated data range here
 * REMEMBER!: timeslot endings must coincide with midday!
 * REMEMBER!: Keep the below format if you change the dates YYYY-MM-DD HH:mm:ss
 **/
define ('_beginDay_H','09');
define ('_beginDay_M','00');
define('_midday_H', '12');
define('_midday_M', '00');
define('_endDay_H', '17');
define('_endDay_M', '59');
define('_interval','+30 minutes');
define('_lunchBreak','+2 hours');
define('_monday', '2014-12-01');
define('_sunday', '2014-12-07');
//testing purpose
use app\models\Timeslot;
use Yii;

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
        return $this->render('index');
    }

    public function actionExecute() {
        //loads and creates staff objects and then saves it to the db
        $staff = require(__DIR__ . '/../tests/codeception/fixtures/staff.php');
        $staff_ids = array();
        foreach ($staff as $ele) {
            $object = $this->map('app\models\Staff', $ele);
            $object->save();
            array_push($staff_ids, $object->id);
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
        $simulators = require(__DIR__ . '/../tests/codeception/fixtures/simulator.php');
        $simulators_ids = array();
        foreach ($simulators as $ele) {
            $object = $this->map('app\models\Simulator', $ele);
            $object->save();
            array_push($simulators_ids, $object->id);
        }
        //This section can be changed to generate time slots in different dates
        $format_string = "Y-m-d H:i:s";
        $sunday = \DateTime::createFromFormat($format_string, _sunday.' '._beginDay_H.':'._beginDay_M.':00');
        $midday = \DateTime::createFromFormat($format_string, _sunday.' '._midday_H.':'._midday_M.':00');
        $weekday = \DateTime::createFromFormat($format_string, _monday.' '._beginDay_H.':'._beginDay_M.':00');
        $interval = \DateInterval::createFromDateString(_interval);
        $lunchBreak = \DateInterval::createFromDateString(_lunchBreak);
        $endDay = \DateTime::createFromFormat($format_string, _sunday.' '._endDay_H.':'._endDay_M.':00');

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
        return $this->render('index');
    }

}
