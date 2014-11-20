<?php

use yii\db\Schema;
use yii\db\Migration;

class m141120_092523_database_creation extends Migration
{
    public function up()
    {
		//table Booking
		$this->createTable('Booking', [
				'id' => 'pk',
				'status' => 'boolean',
				'timestamp' => 'timestamp',
				'name' => 'string',
				'surname' => 'string',
				'telephone' => 'string',
				'e-mail' => 'string',
				'address' => 'string',
				]);
				
		//table TimeSlot		
		$this->createTable('TimeSlot', [
				'id' => 'pk',
				'start' => 'datetime',
				'end' => 'datetime',
				'cost' => 'integer',
				'id_timeSlotModel' => 'integer',
				'id_simulator' => 'integer',
				]);
				
		//table TimeSlotModel
		$this->createTable('TimeSlotModel', [
				'id' => 'pk',
				'start_time' => 'time',
				'end_time' => 'time',
				'frequency' => 'integer',//verificare enum
				'start_validity' => 'date',
				'end_validity' => 'date',
				'repeat_day' => 'integer',//come prima
				'id_simulator' => 'integer',
				]);
				
		//table Simulator		
		$this->createTable('Simulator', [
				'id' => 'pk',
				'name' => 'string',
				'description' => 'text',
				'flight_duration' => 'integer',
				'price_simulation' => 'integer',
				]);
				
		//table Staff
		$this->createTable('Staff', [
				'id' => 'pk',
				'name' => 'string',
				'surname' => 'string',
				'telephone' => 'string',
				'e-mail' => 'string',
				'address' => 'string',
				'role' => 'integer',//enum
				'user_name' => 'string',
				'password' => 'string',
				'last_login' => 'timestamp',
				]);
    }

    public function down()
    {
        echo "m141120_092523_database_creation cannot be reverted.\n";

        return false;
    }
}
