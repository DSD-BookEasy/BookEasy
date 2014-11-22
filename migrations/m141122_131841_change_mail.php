<?php

use yii\db\Schema;
use yii\db\Migration;

class m141122_131841_change_mail extends Migration
{
    public function up()
    {
		//change attribute name from e-mail to email
		//from booking
		$this->renameColumn(
							"booking",
							"e-mail",
							"email"
		);
		//from booking
		$this->renameColumn(
							"staff",
							"e-mail",
							"email"
		);

    }

    public function down()
    {
        echo "m141122_131841_change_mail cannot be reverted.\n";

        return false;
    }
}
