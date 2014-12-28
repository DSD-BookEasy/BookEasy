<?php

use yii\db\Schema;
use yii\db\Migration;

class m141228_153441_insertEmailTextParameter extends Migration
{
    public function up()
    {
        $this->insert('Parameter',
            ['id' => 'emailTextToCoordinator',
            'value' => 'Hello,
                        a new booking for the museum simulators has been received.
                        Check it out here:',
            'name' => 'Email Text to Coordinator',
            'description' => 'This is the body of the email text that is automatically sent to coordinator
            when a booking outside the opening hours is requested'
            ]);
    }

    public function down()
    {
        $this->delete('Parameter', ['id' => 'emailTextToCoordinator']);
    }
}
