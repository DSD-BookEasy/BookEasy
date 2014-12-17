<?php

namespace app\controllers;

use app\models\AdminRole;
use \Yii;

class PermissionsController extends \yii\web\Controller
{
    public function actionRoles()
    {
        return $this->render('roles',[
            'roles' => Yii::$app->authManager->getRoles()
        ]);
    }

    public function actionAddRole(){
        $post=Yii::$app->request->post('AdminRole');
        if(!empty($post) and !empty($post['name'])){
            $new_r=Yii::$app->authManager->createRole($post['name']);
            if(!empty($post['description'])) {
                $new_r->description=$post['description'];
            }
            Yii::$app->authManager->add($new_r);
            $this->redirect(['permissions/roles']);
        }

        return $this->render('add-role',[
            'role' => new AdminRole(Yii::$app->authManager->createRole(''))
        ]);
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
