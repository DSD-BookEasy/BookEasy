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
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

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

        if ( Yii::$app->user->isGuest ) {
            return $this->render('index', [
                'simulators' => $simulators->getModels(),
            ]);
        } else {
            return $this->render('admin-index', [
                'simulators' => $simulators->getModels(),
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}
