<?php

use yii\db\Schema;
use yii\db\Migration;

class m141215_130958_add_token_to_booking extends Migration
{
    public function up()
    {
        $this->addColumn('Booking', 'token', 'string' );
    }

    public function down()
    {
        $this->dropColumn('Booking', 'token');
    }
}
