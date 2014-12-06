<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "TimeSlotModel".
 *
 * @property integer $id
 * @property string $start_time
 * @property string $end_time
 * @property integer $frequency
 * @property string $start_validity
 * @property string $end_validity
 * @property integer $repeat_day
 * @property integer $id_simulator
 * @property string $last_generation
 */
class TimeSlotModel extends \yii\db\ActiveRecord
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
    const  FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timeslotmodel';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_time', 'end_time', 'start_validity', 'end_validity', 'last_generation'], 'safe'],
            [['frequency', 'repeat_day', 'id_simulator'], 'integer']
        ];
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
            'id_simulator' => Yii::t('app', 'Id Simulator'),
            'last_generation' =>  Yii::t('app', 'Last Generation'),
        ];
    }

    public function repeat_day_string(){

        switch($this->repeat_day){
            case 1:
                return "Monday";
            case 2:
                return "Tuesday";
            case 3:
                return "Wednesday";
            case 4:
                return "Thursday";
            case 5:
                return "Friday";
            case 6:
                return "Saturday";
            case 7:
                return "Sunday";
        }
    }
}
