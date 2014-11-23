<?php

use yii\db\Schema;
use yii\db\Migration;

class m141123_214756_staff_authkey extends Migration
{
    public function up()
    {
        $this->addColumn(
          "Staff",
          "auth_key",
          "string"
        );
    }

    public function down()
    {
        $this->dropColumn(
          "Staff",
          "auth_key"
        );
    }
}
