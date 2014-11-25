<?php

namespace app\controllers;

use app\models\Staff;

class StaffController extends \yii\web\Controller
{
    public function actionLogin()
    {
        //Already loggedin users should not access this page
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $loginData=\Yii::$app->request->post('Staff');
        //No data sent, show the form
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
}
