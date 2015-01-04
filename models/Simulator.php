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
            [['uploadFile'], 'image', 'maxSize' => 1.5*1024*1024]
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
            'price_simulation' => Yii::t('app', 'Price'),
            'uploadFile' => Yii::t('app', 'Upload Image'),
        ];
    }

    /**
     * Upload an image related to the simulator
     * @param bool $isMain set the image as tha main one
     */
    public function uploadImage($isMain = true) {

        $this->uploadFile = UploadedFile::getInstance($this, 'uploadFile');


        // Saving, copying and deleting is a workaround to PHP uploading tmp files without an extension and the widget being dumb about it
        // TODO: find a cleaner way to do this and avoid useless copies

        $tmpFolderPath = Yii::getAlias('@webroot') . '/uploads';

        // Check whether the folder in which we will temporary save the uploaded image exists
        if ( !file_exists($tmpFolderPath) ) {
            Yii::info("$tmpFolderPath doesn't exist. It will be created.");
            mkdir($tmpFolderPath);
        }

        // Save the file in the tmp folder with its name and extension
        $this->uploadFile->saveAs($tmpFolderPath. '/' .$this->uploadFile->baseName. '.' .$this->uploadFile->extension);

        // Store (it will be copied) the image using yii2images widget, setting it as the main one
        $this->attachImage($tmpFolderPath. '/' .$this->uploadFile->baseName. '.' .$this->uploadFile->extension, $isMain);

        // Delete the temporary copy of the image, since it is now useless
        unlink($tmpFolderPath. '/' . $this->uploadFile->baseName . '.' . $this->uploadFile->extension);
    }
}
