<?php

namespace app\modules\api\modules\v1\controllers;

use yii\rest\ActiveController;

class BookingController extends ActiveController
{
    public $modelClass = 'app\models\Booking';
}