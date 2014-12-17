<?php

use yii\db\Schema;
use yii\db\Migration;

class m141215_122018_add_blocking_field_to_timeslotmodel extends Migration
{
    public function up()
    {
        $this->addColumn('TimeSlotModel', 'blocking', Schema::TYPE_BOOLEAN . ' DEFAULT FALSE' );
    }

    public function down()
    {
        $this->dropColumn('TimeSlotModel', 'blocking');
    }
}
