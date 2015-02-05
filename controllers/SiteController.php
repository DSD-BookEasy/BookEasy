<?php

namespace app\controllers;

use app\models\Simulator;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        $simulators = new ActiveDataProvider([
            'query' => Simulator::find(),
        ]);
            return $this->render('index', [
                'simulators' => $simulators->getModels(),
            ]);
    }
}
