<?php

namespace app\controllers;

use app\models\Staff;

class StaffController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [//Allow access to logout only if user is logged-in
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
        ];
    }

    /**
     * Login
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        //Already loggedin users should not access this page
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $loginData=\Yii::$app->request->post('Staff');
        //No data sent, show the form, link the controller with the view
        if(empty($loginData)){
            return $this->render('login', [
                'model' => new Staff(),
            ]);
        }
        else{
            $staff=Staff::findOne(['user_name'=>$loginData['user_name']]);
            if(!empty($staff) and $staff->isValidPassword($loginData['password'])){
                \Yii::$app->user->login($staff, 3600*24*30);
                return $this->goHome();
            }
            else{
                $staff=new Staff();
                $staff->user_name=$loginData['user_name'];
                return $this->render('login', [
                    'model' => $staff,
                    'error' => \Yii::t('app','Invalid Username or Password')
                ]);
            }
        }
    }

    /**
     * Logout from the system
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->goBack();
    }
}
