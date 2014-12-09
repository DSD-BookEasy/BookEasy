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

    const STD_FORMAT = 'Y-m-d';
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
        $models =  TimeSlotModel::find()
            ->all(); //load all models

        $today = new \DateTime();

        foreach($models as $model){
            //check this usage of date. Maybe move this control to db condition

            //convert strings to datetime
            if(!$model->end_validity == NULL)
                $end_validity = new \DateTime($model->end_validity);
            else
                $end_validity = NULL;


            if($model->last_generation == NULL)
                $last_generation = $today;
            else
                $last_generation =  new \DateTime($model->last_generation);

            //check if the model is still valid and if
            if(!($end_validity < $today && $end_validity!= NULL) && $last_generation < $until){

                if($end_validity < $until && $end_validity != NULL)
                    $stop =  $end_validity;
                else
                    $stop = $until;


                if($last_generation > new \DateTime($model->start_validity))
                    $start = $last_generation;
                else
                    $start = new \DateTime($model->start_validity);

                Timeslot::createTimeSlotFromModel($model, $start, $stop);
            }
        }
    }

    private static function createTimeSlotFromModel($model, $start, $stop){

        $time_scan = new \DateTime( date('Y-m-d', strtotime('next ' . $model->repeatDayToString(), $start->getTimestamp() )));

        $time_increment = new \DateInterval($model->frequency);

        $i = 0;
        while($time_scan < $stop){
            //create new time slot
            /*
            $temp = new Timeslot();
            $temp->id_simulator = $model->id_simulator;
            $temp->start = date_format($time_scan,"Y-m-d") . $model->start_time;
            $temp->end = date_format($time_scan,"Y-m-d") . $model->end_time;
            */

            Timeslot::createFromModel($model, $time_scan);

            //increment time scan
            date_add($time_scan, $time_increment);
            $i++;
            if($i==3){
                throw new \ErrorException();
            }
        }
    }

    public static function createFromModel(TimeSlotModel $model, $day) {
        $temp = new Timeslot();
        $temp->id_simulator = $model->id_simulator;
        $temp->start = date_format($day,"Y-m-d") . ' ' .  $model->start_time;
        $temp->end = date_format($day,"Y-m-d") . ' ' . $model->end_time;
        if (!$temp->save()) {
            throw new ErrorException();
        }
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start', 'end'], 'checkConsistency'],
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
    public function checkConsistency($attr,$params){
        $connection = \Yii::$app->db;/*
        $command = $connection->createCommand('SELECT * FROM TimeSlot WHERE id_simulator = :simulator &&
                                              date(start) = date(:start)'
                                              );
        $command = bindValue(':simulator',  $this->id_simulator);
        $command = bindValue(':start',  $this->start);
        $slots = $command->queryAll();
*/
        $startDate=strftime("%Y-%m-%d",strtotime($this->start));

        $query = self::find()
            ->where(['id_simulator' => $this->id_simulator]);

        if($this->id != NULL){
            $query->andWhere(['not',['id' => $this->id]]);
        }
        $query->andWhere(['DATE(start)'=>$startDate]);

        $slots = $query->all();

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
        if((strtotime($slot->start) == strtotime($this->start) )&& (strtotime($slot->end) == strtotime($this->end))){
            return true;
        }
        return false;
    }


}
