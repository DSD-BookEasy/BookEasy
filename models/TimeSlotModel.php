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
        $this->end_validity = date('Y-m-d', strtotime($this->end_validity));

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
     * @param $until
     */
    public static function generateNextTimeSlot($until){
        $models =  TimeSlotModel::find()
            ->all(); //load all models

        foreach($models as $model){
            $model->createTimeSlotFromModel($until);
            //check this usage of date. Maybe move this control to db condition
        }
    }

    /**
     * Generates the TimeSlots based on the TimeSlotModel until a given date
     * @param $model
     * @param $start
     * @param $stop
     * @throws ErrorException
     */
    public function createTimeSlotFromModel($until){
        $today = new \DateTime();
        //convert strings to datetime
        if(!$this->end_validity == NULL)
            $endValidity = new \DateTime($this->end_validity);
        else
            $endValidity = NULL;

        if($this->generated_until == NULL)
            $generatedUntil = $today;
        else
            $generatedUntil =  new \DateTime($this->generated_until);

        //check if the model is still valid and if
        if(!($endValidity < $today && $endValidity!= NULL) && $generatedUntil < $until){

            if($endValidity < $until && $endValidity != NULL)
                $stop =  $endValidity;
            else
                $stop = $until;

            if($generatedUntil > new \DateTime($this->start_validity))
                $start = $generatedUntil;
            else
                $start = new \DateTime($this->start_validity);

            $time_scan = new \DateTime( date('Y-m-d', strtotime('next ' . $this->repeatDayToString(), $start->getTimestamp() )));

            $time_increment = new \DateInterval($this->frequency);

            while($time_scan <= $stop){
                Timeslot::createFromModel($this, $time_scan);

                //increment time scan
                date_add($time_scan, $time_increment);
            }
        }
    }


    /**
     * Generates the TimeSlots based on the TimeSlotModel until a given date
     * @param $until
     * @throws ErrorException
     */
    public function spawnTimeSlots($until)
    {

        $stopSpawning = new Datetime($until);
        $endValidity = new DateTime($this->end_validity);

        if ($endValidity < new DateTime()) {
            // Model is already invalid, can't spawn new TimeSlots
            throw new ErrorException();
        }

        if ($stopSpawning > $endValidity) {
            // Requesting to spawn ahead of the validity of the model
            $stopSpawning = $endValidity;
        }

        $currDate = new DateTime();

        if (empty($this->generated_until)) {
            // If this is the first time the generation is performed
            $currDate->modify($this->start_validity);
        } else {
            $currDate->modify($this->generated_until);
        }

        if ($currDate->format('l') !== $this->repeatDayToString()) {
            // If the starting date is not in the week day set for $this->repeat_day
            $currDate->modify('next ' . $this->repeatDayToString());
        }

        while ($currDate <= $stopSpawning) {

            TimeSlot::createFromModel($this, $currDate);

            $currDate->add(new \DateInterval($this->frequency));
        }

        $this->generated_until = $currDate->format('Y-m-d'); // not sure about the format
        $this->last_generation = date('Y-m-d');


        if (!$this->save()) {
            throw new ErrorException();
        }

    }

    public function repeatDayToString(){
        return date('l', strtotime("this week + ($this->repeat_day - 1) days"));

    }
}
