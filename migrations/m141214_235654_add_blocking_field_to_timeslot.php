<?php

use yii\db\Schema;
use yii\db\Migration;

class m141214_235654_add_blocking_field_to_timeslot extends Migration
{
    public function up()
    {
        $this->addColumn('TimeSlot', 'blocking', Schema::TYPE_BOOLEAN . ' DEFAULT FALSE' );
    }

    public function down()
    {
        $this->dropColumn('TimeSlot', 'blocking');
    }
}
