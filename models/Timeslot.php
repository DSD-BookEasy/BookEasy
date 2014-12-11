<?php

namespace app\models;

use DateTime;
use Yii;
use yii\base\ErrorException;
use yii\db\ActiveRecord;

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
class Timeslot extends ActiveRecord
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
     * This method insert in the db a timeslot generated from the model and in the day passed as parameters.
     * @param TimeslotModel $model model that contains data for the timeslot to create
     * @param DateTime $day day of the Timeslot to create
     * @return bool if the operation was successful
     */
    public static function createFromModel(TimeslotModel $model, DateTime $day) {

        $newTS = new Timeslot();
        $newTS->id_simulator = $model->id_simulator;
        $newTS->start = $day->format('Y-m-d') . ' ' . $model->start_time;
        $newTS->end = $day->format('Y-m-d') . ' ' . $model->end_time;
        $newTS->id_timeSlotModel = $model->id;

        return $newTS->save();

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
     * Getter for finding the data of the booking associated with the current timeslot
     * You can access it by calling $timeslot->booking
     * @return \yii\db\ActiveQuery
     */
    public function getBooking()
    {
        // Timeslot has_one Booking via Booking.id -> id_booking
        return $this->hasOne(Booking::className(), ['id' => 'id_booking']);
    }

    /**
     * Getter for finding the data of the simulator associated with the current timeslot
     * You can access it by calling $timeslot->simulator
     * @return \yii\db\ActiveQuery
     */
    public function getSimulator()
    {
        return $this->hasOne(Simulator::className(), ['id' => 'id_simulator']);
    }

    /**
     * Check whether exist an other timeSlot with the same simulator, in the same day, overlapping
     * @return bool
     */
    public function checkConsistency($attr,$params){

        if($this->start >= $this->end){
            $this->addError($attr, 'Start point of timeslot is greater or equal than the end');
            return false;
        }


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
                $this->addError($attr, 'The current timeslot is overlapping with the timeslot with code: ' . $slot->id);
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
