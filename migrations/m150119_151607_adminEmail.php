<?php

use yii\db\Schema;
use yii\db\Migration;
use \app\models\Parameter;

class m150119_151607_adminEmail extends Migration
{
    public function up()
    {
        $p= new Parameter();
        $p->id = 'adminEmail';
        $p->description = 'The email that will be used by the system as sender email for all the automatic emails';
        $p->name = 'Administrator Email';
        $p->value = 'admin@example.com';
        $p->save();
    }

    public function down()
    {
        $p = Parameter::findOne('adminEmail');
        $p->delete();
    }
}
