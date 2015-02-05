<?php

use yii\db\Schema;
use yii\db\Migration;

class m141219_141143_delete_staff_role extends Migration
{
    public function up()
    {
        $this->dropColumn("Staff","role");
    }

    public function down()
    {
        $this->addColumn("Staff","role","integer");
    }
}
