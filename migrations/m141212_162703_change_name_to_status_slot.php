<?php

use yii\db\Schema;
use yii\db\Migration;

class m141212_162703_change_name_to_status_slot extends Migration
{
    public function up()
    {
        $this->renameColumn('TimeSlot', 'status', 'creation_mode');
}

    public function down()
    {
        $this->renameColumn('TimeSlot', 'creation_mode', 'status');
        return false;
    }
}
