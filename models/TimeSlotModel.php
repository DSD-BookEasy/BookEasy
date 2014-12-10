<?php

namespace app\models;

use DateInterval;
use DateTime;
use Yii;
use yii\base\ErrorException;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "TimeSlotModel".
 *
 * @property integer $id Identifier of the TimeSlot Model
 * @property integer $id_simulator Identifier of the Simulator the model is referring to
 * @property string $start_time Starting time of the generated TimeSlots
 * @property string $end_time Ending time of the generated TimeSlots
 * @property string $frequency Frequency of the repetition (see durations in ISO8601)
 * @property string $start_validity Starting date of validity of the model
 * @property string $end_validity Ending date of validity of the model
 * @property integer $repeat_day Day of the week in which the repetitions takes place
 * @property DateTime $last_generation Last time the model was used to generate TimeSlot
 * @property string $generated_until Date of the last generated TimeSlot
 */
class TimeSlotModel extends ActiveRecord
{
    //frequency const
    const DAILY = 'P1D';
    const WEEKLY = 'P1W';

    //repeat day const
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'TimeSlotModel';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_time', 'end_time'], 'safe'],
            [['start_validity', 'end_validity'], 'safe'],
            [['generated_until'], 'safe'],
            [['frequency'], 'string'],
            [['repeat_day', 'id_simulator'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        $event = new ModelEvent;
        $this->trigger(self::EVENT_BEFORE_VALIDATE, $event);

        $this->start_validity = date('Y-m-d', strtotime($this->start_validity));

        if ( !empty($this->end_validity) ) {
            $this->end_validity = date('Y-m-d', strtotime($this->end_validity));
        }

        return $event->isValid;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'start_time' => Yii::t('app', 'Start Time'),
            'end_time' => Yii::t('app', 'End Time'),
            'frequency' => Yii::t('app', 'Frequency'),
            'start_validity' => Yii::t('app', 'Start Validity'),
            'end_validity' => Yii::t('app', 'End Validity'),
            'repeat_day' => Yii::t('app', 'Repeat Day'),
            'id_simulator' => Yii::t('app', 'Simulator'),
            'last_generation' =>  Yii::t('app', 'Last Generation'),
        ];
    }


    /**
     * This method creates all the timeslot for each timeslotmodel in the db, until a given date passed as parameters.
     * @param DateTime $until
     */
    public static function generateNextTimeSlot(DateTime $until){
        $until->modify('23:59:59');

        $models = TimeSlotModel::find()
            ->all(); //load all models

        foreach($models as $model){
            $model->advanceModelGeneration($until);
            //check this usage of date. Maybe move this control to db condition
        }
    }

    /**
     * Generates the TimeSlots based on the TimeSlotModel until a given date
     * @param DateTime $until the date until the generation will be performed
     * @return bool if the operation was successful
     */
    public function advanceModelGeneration(DateTime $until)
    {
        $until->modify('23:59:59');

        $today = new DateTime();

        $startValidity = new DateTime($this->start_validity);

        if (!$this->end_validity == null) {
            $endValidity = new DateTime($this->end_validity);
        } else {
            $endValidity = null;
        }

        if ($this->generated_until == null) {
            $generatedUntil = $today;
        } else {
            $generatedUntil = new DateTime($this->generated_until);
        }

        // Check whether the requested TimeSlots where already covered by a previous generation
        if ($generatedUntil > $until) {
            return false;
        }

        // Check whether the TimeSlotModel is already outside its validity
        if ($endValidity != null && $endValidity < $today) {
            return false;
        }

        // Start the generation from the last one generated (if any), don't start from scratch
        if ($generatedUntil > $startValidity) {
            $start = $generatedUntil;
        } else {
            $start = $startValidity;
        }

        // Check whether $until is outside the validity scope of the TimeSlotModel. If so, the generation
        // will stop at the correct validity boundary
        if ($endValidity < $until && $endValidity != null) {
            $stop = $endValidity;
        } else {
            $stop = $until;
        }

        return $this->createTimeSlots($start, $stop);

    }

    /**
     * Create TimeSlots based on $this in between the given dates
     * @param DateTime $from starting date of the range
     * @param DateTime $to ending date date of the range (included)
     * @throws ErrorException
     * @return bool if the operation was successful
     */
    public function createTimeSlots(DateTime $from, DateTime $to)
    {
        $to->modify('23:59:59');

        $time_scan = clone $from;

        if ($from->format('l') !== $this->repeatDayToString()) {
            $time_scan->modify('next ' . $this->repeatDayToString());
        } else {
            // probably redundant
            $time_scan->modify('this ' . $this->repeatDayToString());
        }

        $time_increment = new DateInterval($this->frequency);

        $result = true;

        while ($time_scan <= $to && $result) {
            $result &= Timeslot::createFromModel($this, $time_scan);

            //increment time scan
            $time_scan->add($time_increment);
        }

        return $result;

    }


    /**
     * Deletes the TimeSlots generated by this TimeSlotModel between the given dates
     * @param $from
     * @param $to
     * @return bool if the operation was successful
     */
    public function deleteTimeSlots(DateTime $from, DateTime $to)
    {
        $to->modify('23:59:59');

        $result = true;

        $timeslots = TimeSlot::find()
            ->where(['id_timeSlotModel' => $this->id])
            ->andWhere(['between', 'start', $from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 23:59:59')])
            ->all();

        foreach ($timeslots as $timeslot) {
            $result &= $timeslot->delete();
        }

        return $result;
    }

    /**
     * Translates the repeat_day property to the corresponding day of the week as string (e.g. 1 => 'Monday')
     * @return string
     */
    public function repeatDayToString(){
        return date('l', strtotime("this week + ($this->repeat_day - 1) days"));

    }


}
