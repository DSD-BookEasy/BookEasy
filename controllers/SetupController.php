<?php

namespace app\controllers;

use \Yii;
use \yii\web\BadRequestHttpException;
use \app\models\Staff;

/**
 * This controllers executes the initial setup of the system,
 * like inserting the first admin user and roles
 * This should be executed AFTER all the migrations have been applied to the system
 * @package app\controllers
 */
class SetupController extends \yii\web\Controller
{
    //The predefined roles to put in the system
    private $roles=[
        'Admin' => 'Admins are responsible for the configuration of the system',//First role defined receives all permissions!
        'Coordinator' => 'Coordinators manage bookings and special requests',
        'Instructor' => 'Instructors are assigned to bookings and teach to the customers in the simulators',
    ];

    public function actionIndex()
    {
        //Detect if setup has already been executed on the system
        $adminRoles=Yii::$app->authManager->getRoles();
        if(count($adminRoles)>0){
            throw new BadRequestHttpException("Setup has already been executed!");
        }

        if(Yii::$app->request->getIsPost()){
            $user=new Staff();
            $user->load(Yii::$app->request->post());
            $user->email = 'tmp@mail.com';
            $user->name = 'admin_name';
            $user->surname = 'admin_surname';
            if($user->save()) {
                //Add roles in the system
                $firstRole=null;
                foreach ($this->roles as $roleName => $rDesc) {
                    $newR = Yii::$app->authManager->createRole($roleName);
                    $newR->description = $rDesc;
                    Yii::$app->authManager->add($newR);
                    if($firstRole===null){
                        $firstRole = $newR;
                    }
                }

                //Assign all permissions to the first role (Admin)
                if($firstRole!==null){
                    foreach(Yii::$app->authManager->getPermissions() as $p){
                        Yii::$app->authManager->addChild($firstRole,$p);
                    }
                }

                Yii::$app->authManager->assign($firstRole,$user->id);
                $this->goHome();
            }
        }

        return $this->render('index',[
                'user' => new Staff()
            ]);
    }

}
