<?php

namespace app\models;

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
    const DAILY = 1;
    const WEEKLY = 7;
    const DAILY_INCREMENT = '1D';
    const WEEKLY_INCREMENT = '1W';

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
            [['start_time', 'end_time'], 'date', 'format' => 'php:H:i'],
            [['start_validity', 'end_validity'], 'date', 'format' => 'php:Y-m-d'],
            [['last_generation'], 'date'],
            [['frequency', 'repeat_day', 'id_simulator'], 'integer']
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

    public function repeat_day_string(){
        // Is this really needed for something?
        return date('l', strtotime("this week + ($this->repeat_day - 1) days"));

    }
}
