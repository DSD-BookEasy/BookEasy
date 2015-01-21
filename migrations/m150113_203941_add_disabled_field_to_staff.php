<?php

use yii\db\Schema;
use yii\db\Migration;

class m150113_203941_add_disabled_field_to_staff extends Migration
{
    public function up()
    {
        $this->addColumn('Staff', 'disabled', Schema::TYPE_BOOLEAN . ' DEFAULT FALSE' );
    }

    public function down()
    {
        $this->dropColumn('Staff', 'disabled');
    }
}
