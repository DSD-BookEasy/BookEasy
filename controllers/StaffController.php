<?php

namespace app\controllers;

use Yii;
use app\models\Staff;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class StaffController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [//Allow access to logout only if user is logged-in
                'class' => AccessControl::className(),
                'only' => ['logout', 'index', 'update'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index','update'],
                        'allow' => true,
                        'roles' => ['manageStaff']
                    ]
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
        if (!Yii::$app->user->isGuest) {
            return $this->goBack();
        }
        $loginData=Yii::$app->request->post('Staff');
        //No data sent, show the form, link the controller with the view
        if(empty($loginData)){
            return $this->render('login', [
                'model' => new Staff(),
            ]);
        }
        else{
            $staff=Staff::findOne(['user_name'=>$loginData['user_name']]);
            if(!empty($staff) and $staff->isValidPassword($loginData['password'])){
                Yii::$app->user->login($staff, 3600*24*30);
                return $this->goBack('site/index');
            }
            else{
                $staff=new Staff();
                $staff->user_name=$loginData['user_name'];
                return $this->render('login', [
                    'model' => $staff,
                    'error' => Yii::t('app','Invalid Username or Password')
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

    /**
     * Shows a list of all the users in the system
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Staff::find(),
        ]);

        return $this->render('index', [
            'users' => $dataProvider,
        ]);
    }

    /**
     * Shows a form to edit the informations of a user
     * @param integer $id the id of the user to edit
     * @return string
     */
    public function actionUpdate($id){
        $s=Staff::findOne((int)$id);
        if(empty($s)){
            throw new NotFoundHttpException(Yii::t('app',"The specified user doesn't exist"));
        }

        if(Yii::$app->request->getIsPost()) {
            $s->load(Yii::$app->request->post());
            if($s->save()){//If basic save is successfull, go on with permissions save
                $this->updateRoles($s,Yii::$app->request->post('roles',[]));
            }
        }

        return $this->render('update',[
            'user' => $s,
            'allRoles' => Yii::$app->authManager->getRoles(),
            'roles' => Yii::$app->authManager->getRolesByUser($s->id)
        ]);
    }

    /**
     * Updates the roles assigned to a user basing on the input from the POST
     * @param Staff $user the user to update
     * @param array $roles an array of roles to add. It should be indexed with the names of the roles
     */
    private function updateRoles($user,$roles)
    {
        $oldRoles = Yii::$app->authManager->getRolesByUser($user->id);

        $toDelete = array_diff_key($oldRoles,$roles);
        $toAdd = array_diff_key($roles, $oldRoles);

        foreach($toDelete as $roleName => $rObj){
            Yii::$app->authManager->revoke($rObj,$user->id);
        }

        foreach($toAdd as $roleName => $value){
            $r = Yii::$app->authManager->getRole($roleName);
            Yii::$app->authManager->assign($r,$user->id);
        }
    }
}
