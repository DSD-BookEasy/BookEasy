<?php

namespace app\controllers;

use \Yii;
use yii\rbac\Role;

class PermissionsController extends \yii\web\Controller
{
    public function actionAddRole()
    {
        $post=Yii::$app->request->post();
        if(!empty($post)){

        }
        return $this->render('add-role');
    }

    public function actionIndex()
    {
        $post=Yii::$app->request->post();
        if(!empty($post)){

        }
        return $this->render('index');
    }

}
