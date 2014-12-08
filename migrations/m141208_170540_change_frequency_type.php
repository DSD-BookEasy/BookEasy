<?php

use yii\db\Schema;
use yii\db\Migration;

class m141208_170540_change_frequency_type extends Migration
{
    public function up()
    {
        $this->alterColumn('TimeSlotModel', 'frequency', Schema::TYPE_STRING);
    }

    public function down()
    {
        $this->alterColumn('TimeSlotModel', 'frequency', Schema::TYPE_INTEGER);
    }
}
