<?php

use yii\db\Schema;
use yii\db\Migration;

class m141124_135932_addTimeSlotBookingKey extends Migration
{
    public function up()
    {
        $this->addColumn('TimeSlot', 'id_booking','integer');
    }

    public function down()
    {
        $this->dropColumn('TimeSlot', 'id_booking');

        return false;
    }
}
