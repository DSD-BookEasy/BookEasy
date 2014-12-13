<?php

use yii\db\Schema;
use yii\db\Migration;

class m141212_155157_add_status_to_timeslot extends Migration
{
    public function up()
    {
        $this->addColumn(
            "TimeSlot",
            "status",
            'integer' . " DEFAULT 0"
        );
    }

    public function down()
    {
        $this->dropColumn(
            "TimeSlot",
            "status"
        );
    }
}

