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

    public function getTimeslots() {
        // Booking has_many Timeslot via timeslot.id_booking -> id
        return $this->hasMany(Timeslot::className(), ['id_booking' => 'id']);
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->token = Yii::$app->getSecurity()->generateRandomString($length = 6);
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


    /*
     * old status to string
    public function statusToString(){
        switch($this->status){
            case Booking::CONFIRMED:
                $this->status = 'Confirmed';
                break;
            case Booking::NOT_CONFIRMED:
                $this->status = 'Not Confirmed';
                break;
            case Booking::WAITING_FOR_CONFIRMATION:
                $this->status = 'Waiting for Confirmation';
                break;
        }
    }
    */

}