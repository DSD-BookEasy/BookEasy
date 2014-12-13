<?php

use yii\db\Schema;
use yii\db\Migration;

class m141129_141506_booking_assigned_instructor extends Migration
{
    public function up()
    {
        $this->addColumn("Booking",'assigned_instructor','integer');
    }

    public function down()
    {
        $this->dropColumn("Booking",'assigned_instructor');

        return false;
    }
}
