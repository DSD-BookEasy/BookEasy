<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Parameter".
 * It stores parameters of the system that can be changed by the customer
 *
 * @property string $id
 * @property string $value
 * @property string $name
 * @property string $description
 * @property string $last_update
 */
class Parameter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Parameter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['value', 'description'], 'string'],
            [['id','name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'value' => Yii::t('app', 'Value'),
            'last_update' => Yii::t('app', 'Last Update'),
            'description' => Yii::t('app', 'Description'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * Quick method for finding the value of a parameter
     * @param string $name the name of the parameter
     * @param mixed $default optional. The value to return if the parameter doesn't exist.
     * @return string|null the value of $name or $default if the parameter doesn't exist
     */
    static public function getValue($name,$default=null)
    {
        $p=self::findOne(['id'=>$name]);
        if($p===null){
            return $default;
        }
        else{
            return $p->value;
        }
    }
}
