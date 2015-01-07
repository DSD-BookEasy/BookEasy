<?php

namespace app\controllers;

use app\models\Timeslot;
use DateTime;
use Yii;
use app\models\Simulator;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class SimulatorFullController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
