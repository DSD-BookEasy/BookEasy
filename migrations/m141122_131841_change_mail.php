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
    	    "Booking",
    	    "e-mail",
    	    "email"
    	);
    	//from booking
    	$this->renameColumn(
    	    "Staff",
    	    "e-mail",
    	    "email"
	    );
    }

    public function down()
    {
        $this->renameColumn(
	        "Booking",
	        "email",
	        "e-mail"
	    );
	
	//from booking
	    $this->renameColumn(
	        "Staff",
	        "email",
	        "e-mail"
	    );
    }
}
