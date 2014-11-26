<?php

namespace app\modules\api\modules\v1\controllers;

use Yii;
use app\models\Staff;
use app\models\StaffSearch;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

/**
 * StaffController implements the CRUD actions for Staff model.
 */
class StaffController extends ActiveController
{

    public $modelClass = 'app\models\Staff';

}
