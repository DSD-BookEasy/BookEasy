<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "timeslot".
 *
 * @property integer $id
 * @property string $start
 * @property string $end
 * @property integer $cost
 * @property integer $id_timeSlotModel
 * @property integer $id_simulator
 */
class TimeSlot extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timeslot';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start', 'end'], 'safe'],
            [['cost', 'id_timeSlotModel', 'id_simulator'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'start' => Yii::t('app', 'Start'),
            'end' => Yii::t('app', 'End'),
            'cost' => Yii::t('app', 'Cost'),
            'id_timeSlotModel' => Yii::t('app', 'Id Time Slot Model'),
            'id_simulator' => Yii::t('app', 'Id Simulator'),
        ];
    }

    /**
     * Check whether exist an other timeSlot with the same simulator, in the same day, overlapping
     * @return bool
     */
    public function checkConsistency($id=NULL){
        $connection = \Yii::$app->db;/*
        $command = $connection->createCommand('SELECT * FROM TimeSlot WHERE id_simulator = :simulator &&
                                              date(start) = date(:start)'
                                              );
        $command = bindValue(':simulator',  $this->id_simulator);
        $command = bindValue(':start',  $this->start);
        $slots = $command->queryAll();
*/
        $condition = ['id_simulator' => $this->id_simulator,
            'DATE(start)=DATE(:start)'];

        if($id != NULL){
            $condition[] = ['not',['id' => $id]];
        }

        $slots = TimeSlot::find()
            ->where($condition,
                [':start' => $this->start])
            ->all();

        foreach($slots as $slot){
            if($this->overlapping($slot))
                return false;
        }
        return true;
    }

    public function overlapping($slot){
        if((strtotime($slot->start) > strtotime($this->start) )&& (strtotime($slot->start) < strtotime($this->end))){
            return true;
        }
        if((strtotime($slot->end) > strtotime($this->start) )&& (strtotime($slot->end) < strtotime($this->end))){
            return true;
        }
        if((strtotime($this->start) > strtotime($slot->start) )&& (strtotime($this->start) < strtotime($slot->end))){
            return true;
        }
        if((strtotime($this->end) > strtotime($slot->start) )&& (strtotime($this->end) < strtotime($slot->end))){
            return true;
        }
        if((strtotime($slot->start) == strtotime($this->start) )&& (strtotime($slot->start) == strtotime($this->end))){
            return true;
        }
        return false;
    }

}
