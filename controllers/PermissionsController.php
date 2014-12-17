<?php

namespace app\controllers;

use \Yii;
use yii\rbac\Role;

class PermissionsController extends \yii\web\Controller
{
    public function actionRoles()
    {
        return $this->render('roles',[
            'roles' => Yii::$app->authManager->getRoles()
        ]);
    }

    public function actionAddRole(){

    }

    public function actionUpdateRole(){

    }

    public function actionDeleteRole(){

    }

    public function actionIndex()
    {
        $post=Yii::$app->request->post();
        if(!empty($post)){

        }
        return $this->render('index');
    }

}
