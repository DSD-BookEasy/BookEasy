<?php

namespace app\models;

use Yii;
use DateTime;
/**
 * This is the model class for table "Simulator".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $flight_duration
 * @property integer $price_simulation
* @property DateTime $date



 */
class Simulator extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return 'Simulator';
    }

    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [['description'], 'string'],
            [['flight_duration', 'price_simulation'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['selDate'],'date'],



        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'flight_duration' => Yii::t('app', 'Flight Duration'),
            'price_simulation' => Yii::t('app', 'Price Simulation'),
        ];
    }
}
