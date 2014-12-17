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
        return [

            [['status'], 'integer'],
            [['timestamp','id','token'], 'safe'],
            [['name', 'surname', 'telephone', 'email', 'address'], 'string', 'max' => 255],
            [['comments'], 'string', 'max' => 255],
            ['email', 'email'],
            [['name', 'surname'], 'required'],
            ['email', 'required', 'on' => ['weekdays']],
            [['id', 'name', 'surname', 'token'], 'required', 'on' => ['search']]

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
            'token' => Yii::t('app', 'Secret Key')
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


}