<?php

use yii\db\Schema;
use yii\db\Migration;

class m141203_181841_parameters_table extends Migration
{
    public function up()
    {
        $this->createTable('Parameter', [
            'id' => 'string NOT NULL PRIMARY KEY',
            'value' => 'text',
            'name' => 'string',
            'description' => 'text',
            'last_update' => 'timestamp'
        ], 'ENGINE InnoDB');

        $p=new \app\models\Parameter();
        $p->id="entryFee";
        $p->description="The price to enter the museum, in SEK";
        $p->name="Entry Fee";
        $p->value="80";
        $p->save();

        $p=new \app\models\Parameter();
        $p->id="coordinatorEmail";
        $p->description="The email where to send emails when a new booking is made";
        $p->name="Coordinator's Email";
        $p->value="bokning@flygmuseum.com";
        $p->save();
    }

    public function down()
    {
        $this->dropTable('Parameter');
    }
}
