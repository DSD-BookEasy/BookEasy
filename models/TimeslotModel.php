<?php

namespace app\models;

use DateInterval;
use DateTime;
use Yii;
use yii\base\ErrorException;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "TimeslotModel".
 *
 * @property integer $id Identifier of the TimeslotModel
 * @property integer $id_simulator Identifier of the Simulator the model is referring to
 * @property string $start_time Starting time of the generated Timeslots
 * @property string $end_time Ending time of the generated Timeslots
 * @property string $frequency Frequency of the repetition (see durations in ISO8601)
 * @property string $start_validity Starting date of validity of the model
 * @property string $end_validity Ending date of validity of the model
 * @property integer $repeat_day Day of the week in which the repetitions takes place
 * @property string $generated_until Date of the last generated Timeslot
 */
class TimeslotModel extends ActiveRecord
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
     * Allows to update Timeslot's validity
     * @param TimeslotModel $new
     * @return bool if the operation was successful
     */
/*    public function updateModel(TimeslotModel $new)
    {
        $result = true;

        if ($new->start_validity !== $this->start_validity) {

            if ($new->start_validity < $this->start_validity) {
                $result &= $this->createTimeslots(new DateTime($new->start_validity), new DateTime($this->start_validity));
            } else {
                $result &= $this->deleteTimeslots(new DateTime($this->start_validity), new DateTime($$new->start_validity));
            }

            $this->start_validity = $new->start_validity;

        }

        if ($new->end_validity !== $this->end_validity) {

            if ($new->end_validity < $this->end_validity) {
                $result &= $this->deleteTimeslots(new DateTime($new->end_validity), new DateTime($this->end_validity));

            } else {
                $result &= $this->createTimeslots(new DateTime($this->end_validity), new DateTime($new->end_validity));

            }

            $this->end_validity = $new->end_validity;

        }

        if ( $result ) {
            return $this->save();
        }

        return false;

    }*/

    /**
     * Deletes the TimeslotModel with the Timeslots that were generated from it
     * @return bool
     */
    public function deleteModel()
    {
        $this->deleteTimeslots(new DateTime($this->start_validity), new DateTime($this->end_validity));

        return $this->delete();
    }


    /**
     * This method creates all the timeslot for each timeslotmodel in the db, until a given date passed as parameters.
     * @param DateTime $until
     */
    public static function generateNextTimeslot(DateTime $until){
        $until->modify('23:59:59');

        $models = TimeslotModel::find()
            ->all(); //load all models

        foreach($models as $model){
            $model->advanceModelGeneration($until);
            //check this usage of date. Maybe move this control to db condition
        }
    }

    /**
     * Generates the Timeslots based on the TimeslotModel until a given date
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

        // Check whether the requested Timeslots where already covered by a previous generation
        if ($generatedUntil > $until) {
            return false;
        }

        // Check whether the TimeslotModel is already outside its validity
        if ($endValidity != null && $endValidity < $today) {
            return false;
        }

        // Start the generation from the last one generated (if any), don't start from scratch
        if ($generatedUntil > $startValidity) {
            $start = $generatedUntil;
        } else {
            $start = $startValidity;
        }

        // Check whether $until is outside the validity scope of the TimeslotModel. If so, the generation
        // will stop at the correct validity boundary
        if ($endValidity < $until && $endValidity != null) {
            $stop = $endValidity;
        } else {
            $stop = $until;
        }

        return $this->createTimeslots($start, $stop);

    }

    /**
     * Create Timeslots based on $this in between the given dates
     * @param DateTime $from starting date of the range
     * @param DateTime $to ending date date of the range (included)
     * @throws ErrorException
     * @return bool if the operation was successful
     */
    public function createTimeslots(DateTime $from, DateTime $to)
    {
        $from->modify('00:00:00');
        $to->modify('23:59:59');

        $time_scan = clone $from;

        // Account for the fact that $from could be in a different week day from the one set by the repetition
        if ($from->format('l') !== $this->repeatDayToString() && $this->frequency == TimeslotModel::WEEKLY) {
            $time_scan->modify('next ' . $this->repeatDayToString());
        }

        $time_increment = new DateInterval($this->frequency);


        while ($time_scan <= $to) {
            Timeslot::createFromModel($this, $time_scan);

            $time_scan->add($time_increment);
        }

        $this->generated_until = $to->format('Y-m-d');

        return $this->save();;

    }


    /**
     * Deletes the Timeslots generated by this TimeslotModel between the given dates
     * @param $from
     * @param $to
     * @return bool if the operation was successful
     */
    public function deleteTimeslots(DateTime $from, DateTime $to)
    {
        $to->modify('23:59:59');

        $result = true;

        $timeslots = Timeslot::find()
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
        return date('l', strtotime("this Sunday + $this->repeat_day days"));

    }


}
