<?php

namespace app\models;

use Yii;
use DateTime;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "Simulator".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $flight_duration
 * @property integer $price_simulation
 * @property UploadedFile $uploadFile
 *
 * The following methods are provided by the behavior rico\yii2images\behaviors\ImageBehave
 * @method attachImage($absolutePath, $isMain = false)
 * @method setMainImage($img)
 * @method clearImagesCache()
 * @method getImages()
 * @method getImage()
 * @method removeImages()
 * @method removeImage(Image $img)
 * @see https://github.com/CostaRico/yii2-images
 */
class Simulator extends ActiveRecord
{
    public $uploadFile;

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
            [['uploadFile'], 'image']
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'image' => [
                'class' => 'rico\yii2images\behaviors\ImageBehave',
            ]
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
