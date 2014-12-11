<?php

use yii\db\Schema;
use yii\db\Migration;

class m141211_131041_remove_last_generation_col extends Migration
{
    public function up()
    {
        $this->dropColumn('TimeSlotModel', 'last_generation');
    }

    public function down()
    {
        $this->addColumn('TimeSlotModel', 'last_generation','date');
    }

}
