<?php

use yii\db\Schema;
use yii\db\Migration;

class m141208_154411_add_last_generated extends Migration
{
    public function up()
    {
        $this->addColumn(
            "TimeSlotModel",
            "generated_until",
            Schema::TYPE_DATE
        );
    }

    public function down()
    {
        $this->dropColumn(
            "TimeSlotModel",
            "generated_until"
        );
    }
}
