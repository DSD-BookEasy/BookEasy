<?php

use yii\db\Schema;
use yii\db\Migration;

class m150118_191353_add_password_recover_field extends Migration
{
    public function up()
    {
        $this->addColumn("Staff",'last_recover','datetime');
        $this->addColumn("Staff",'recover_hash','string');
    }

    public function down()
    {
        $this->dropColumn("Staff",'last_recover');
        $this->dropColumn("Staff",'recover_hash');
    }
}
