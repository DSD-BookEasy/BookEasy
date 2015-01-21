<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;


/**
 * This is the model class for table "booking".
 *
 * @property integer $id
 * @property integer $status
 * @property string $timestamp
 * @property string $name
 * @property string $surname
 * @property string $telephone
 * @property string $email
 * @property string $address
 * @property string $comments
 * @property integer $assigned_instructor
 * @property string $token
 *
 * Linked Models
 * @property Timeslot[] $timeslots
 * @property Staff $instructors
 */
class Booking extends \yii\db\ActiveRecord
{

    const CONFIRMED = 1;
    const NOT_CONFIRMED = 0;
    const WAITING_FOR_CONFIRMATION = 2;

    public $assigned_instructor_name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Booking';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $searchWithoutToken = Yii::$app->user->can('manageBookings');
        return [

            [['status'], 'integer'],
            [['timestamp','id','token'], 'safe'],
            [['name', 'surname', 'telephone', 'email', 'address'], 'string', 'max' => 255],
            [['comments'], 'string', 'max' => 255],
            ['email', 'email'],
            ['telephone','number', 'message'=>'The phone number must contain only number'],
            [['name', 'surname', 'email'], 'required'],
            [['token'], 'required', 'on' => ['search'], 'strict' => $searchWithoutToken]

        ];
    }

    /**
     * @inheritdoc
     */



    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'status' => Yii::t('app', 'Status'),
            'timestamp' => Yii::t('app', 'Booking Created on'),
            'name' => Yii::t('app', 'Name'),
            'surname' => Yii::t('app', 'Surname'),
            'telephone' => Yii::t('app', 'Telephone'),
            'email' => Yii::t('app', 'Email'),
            'address' => Yii::t('app', 'Address'),
            'comments' => Yii::t('app', 'Your comments'),
            'token' => Yii::t('app', 'Secret Key'),
            'assigned_instructor_name' => Yii::t('app', 'Assigned Instructor'),
            'assigned_instructor' => Yii::t('app', 'Assigned Instructor')
        ];
    }

    /**
     * Represents the relationship between a Booking and the timeslots
     * You can access timeslots associated to a booking by calling
     * $booking->timeslots
     * @return \yii\db\ActiveQuery
     */
    public function getTimeslots() {
        // Booking has_many Timeslot via timeslot.id_booking -> id
        return $this->hasMany(Timeslot::className(), ['id_booking' => 'id']);
    }

    /**
     * Represents the relationship between a Booking and an Instructor
     * You can access the instructor object by calling
     * $booking->instructor
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInstructor() {
        return $this->hasOne(Staff::className(), ['id' => 'assigned_instructor']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->token = Yii::$app->getSecurity()->generateRandomString($length = 6);

                if(empty($this->timestamp)){
                    $now = new \DateTime();
                    $this->timestamp = $now->format('Y-m-d H:i');
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Return the string that correspond to the semantic value of the status
     * @return string
     */
    public function statusToString(){
        switch($this->status){
            case Booking::CONFIRMED:
                return Yii::t('app', 'Confirmed');
            case Booking::NOT_CONFIRMED:
                return Yii::t('app', 'Not Confirmed');
            case Booking::WAITING_FOR_CONFIRMATION:
                return Yii::t('app', 'Waiting for Confirmation');
        }
    }
}