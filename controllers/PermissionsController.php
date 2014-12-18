<?php

namespace app\controllers;

use app\models\AdminRole;
use \Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class PermissionsController extends \yii\web\Controller
{
    /**
     * Shows an index of all available roles
     * @return string
     */
    public function actionRoles()
    {
        return $this->render('roles', [
            'roles' => Yii::$app->authManager->getRoles()
        ]);
    }

    /**
     * Adds a new role in the system
     * @return string
     */
    public function actionAddRole()
    {
        $post = Yii::$app->request->post('AdminRole');
        if (!empty($post) and !empty($post['name'])) {
            $new_r = Yii::$app->authManager->createRole($post['name']);
            if (!empty($post['description'])) {
                $new_r->description = $post['description'];
            }
            Yii::$app->authManager->add($new_r);
            $this->redirect(['permissions/roles']);
        }

        return $this->render('add-role', [
            'role' => new AdminRole(Yii::$app->authManager->createRole(''))
        ]);
    }

    /**
     * Updates an existing role
     * @param $name : the name of the role to update
     */
    public function actionUpdateRole($name)
    {
        $r = Yii::$app->authManager->getRole($name);
        if (empty($r)) {
            throw new NotFoundHttpException("The specified role could not be found");
        }

        $post = Yii::$app->request->post('AdminRole');
        if (!empty($post) and !empty($post['name'])) {
            $r->name = $post['name'];
            if (!empty($post['description'])) {
                $r->description = $post['description'];
            }
            Yii::$app->authManager->update($name, $r);
            $this->redirect(['permissions/roles']);
        }

        return $this->render('add-role', [
            'role' => new AdminRole($r)
        ]);
    }

    /**
     * Deletes an existing role
     * @param $name : the name of the role to delete
     */
    public function actionDeleteRole($name)
    {
        $r = Yii::$app->authManager->getRole($name);
        if (empty($r)) {
            throw new NotFoundHttpException("The specified role could not be found");
        }

        $post = Yii::$app->request->post();

        if (empty($post)) {
            echo $this->render('delete-role', [
                'role' => new AdminRole($r)
            ]);
        } else {
            Yii::$app->authManager->remove($r);
            $this->redirect('roles');
        }
    }

    /**
     * Show all permissions assigned to the roles
     * @return string
     */
    public function actionIndex()
    {
        if(Yii::$app->request->getIsPost()) {
            $this->updatePermissions(Yii::$app->request->post('permissions', []));
        }

        $roles = Yii::$app->authManager->getRoles();
        $assignments = [];
        foreach ($roles as $r) {
            $assignments[$r->name] = Yii::$app->authManager->getPermissionsByRole($r->name);
        }

        return $this->render('index', [
            'roles' => $roles,
            'permissions' => Yii::$app->authManager->getPermissions(),
            'assignments' => $assignments
        ]);
    }

    /**
     * Updates the permissions associated to each role based on the POST input
     * @param $post the permissions array coming from the POST request
     */
    private function updatePermissions($post)
    {
        $roles=Yii::$app->authManager->getRoles();
        foreach($roles as $roleName => $role){
            $permissions=empty($post[$roleName])? []:$post[$roleName];
            $oldPerms = Yii::$app->authManager->getPermissionsByRole($role->name);

            $toDelete = array_diff_key($oldPerms,$permissions);
            $toAdd = array_diff_key($permissions,$oldPerms);

            foreach($toDelete as $perm){
                Yii::$app->authManager->removeChild($role,$perm);
            }

            foreach($toAdd as $perm => $value){
                $pObj=Yii::$app->authManager->getPermission($perm);
                if(!empty($pObj)){
                    Yii::$app->authManager->addChild($role,$pObj);
                }
            }
        }
    }
}
