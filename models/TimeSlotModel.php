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
 */
class TimeSlotModel extends \yii\db\ActiveRecord
{
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
            [['start_time', 'end_time', 'start_validity', 'end_validity'], 'safe'],
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
        ];
    }
}
