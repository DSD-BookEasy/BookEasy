<?php

use yii\db\Schema;
use yii\db\Migration;

class m141206_152140_addLastGeneration extends Migration
{
    public function up()
    {
		$this->addColumn('TimeSlotModel', 'last_generation','date');
    }

    public function down()
    {
		$this->dropColumn('TimeSlotModel', 'last_generation');
    }
}
