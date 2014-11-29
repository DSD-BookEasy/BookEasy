<?php

use yii\db\Schema;
use yii\db\Migration;

class m141129_135635_addAdditionalInformation extends Migration
{
    public function up()
    {
        $this->addColumn('Booking', 'comments','string');
    }

    public function down()
    {
        $this->dropColumn('Booking', 'comments');

        return false;
    }
}
