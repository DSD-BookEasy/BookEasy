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
 * @property integer $id_booking
 */
class Timeslot extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'TimeSlot';
    }

    /**
     * @param $until date
     */
    public static function generateNextTimeSlot($until){
        $models =  TimeSlotModel::findAll(''); //load all models
        $today = strtotime(date("Y-m-d"));

        foreach($models as $model){
            //check this usage of date. Maybe move this control to db condition

            //convert strings to datetime
            $end_validity = strtotime($model->end_validity);
            if($model->last_generation == NULL)
                $last_generation = $today;
            else
                $last_generation = strtotime($model->last_generation);

            //check if the model is still valid and if
            if(!($end_validity < $today) && $last_generation < $until){
                if($end_validity < $until){
                    $stop =  $end_validity;
                }else{
                    $stop = $until;
                }

                if($last_generation > strtotime($model->start_validity)){
                    $start = $last_generation;
                }else
                    $start = strtotime($model->start_validity);

                createTimeSlotFromModel($model, $start, $stop);
            }
        }
    }

    private function createTimeSlotFromModel($model, $start, $stop){

        $time_scan = strtotime('next ' . $model->repeat_day_string(), $start);

        //map frequency with its increment
        switch($model->frequency){
            case TimeSlotModel::DAILY:
                $time_increment = new \DateInterval(TimeSlotModel::DAILY_INCREMENT);
                break;
            case TimeSlotModel::WEEKLY:
                $time_increment = new \DateInterval(TimeSlotModel::WEEKLY_INCREMENT);
                break;
            default:
                throw new \Exception();
        }

        while($time_scan < $stop){
            //create new time slot
            $temp = new Timeslot();
            $temp->id_simulator = $model->id_simulator;
            $temp->start = date_format($time_scan,"Y-m-d") . $model->start_time;
            $temp->end = date_format($time_scan,"Y-m-d") . $model->end_time;
            //increment time scan
            date_add($time_scan, $time_increment);
        }
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

    public function getBooking()
    {
        // Timeslot has_one Booking via Booking.id -> id_booking
        return $this->hasOne(Booking::className(), ['id' => 'id_booking']);
    }

    /**
     * Check whether exist an other timeSlot with the same simulator, in the same day, overlapping
     * @return bool
     */
    public function checkConsistency($attr,$params){
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

        if($this->id != NULL){
            $condition[] = ['not',['id' => $this->id]];
        }

        $slots = self::find()
            ->where($condition,
                [':start' => $this->start])
            ->all();

        foreach($slots as $slot){
            if($this->overlapping($slot)) {
                $this->addError($attr, 'The current timeslot is overlapping with existing ones');
                return false;
            }
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
