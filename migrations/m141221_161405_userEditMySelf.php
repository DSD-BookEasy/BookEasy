<?php

use yii\db\Schema;
use yii\db\Migration;
use app\rbac\MyProfile;

class m141221_161405_userEditMySelf extends Migration
{
    /**
     * Adds permission to edit  your own profile
     */
    public function up()
    {
        $auth= Yii::$app->authManager;
        $rule=new MyProfile();
        $auth->add($rule);

        $p=$auth->createPermission("updateOwnProfile");
        $p->description="update his own profile";
        $p->ruleName=$rule->name;
        $auth->add($p);
    }

    public function down()
    {
        $auth= Yii::$app->authManager;
        $rule=new MyProfile();
        $p=$auth->getPermission("updateOwnProfile");
        $auth->remove($p);
        $auth->remove($rule);
    }
}
