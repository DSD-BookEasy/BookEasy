<?php

use yii\db\Schema;
use yii\db\Migration;

class m141219_154327_initial_permissions_set extends Migration
{
    private $p=[
        'manageTimeslots' => 'manage single timeslots availble in the agendas',
        'manageTimeslotModels' => 'manage recurring timeslots',
        'manageParams' => 'change the values of system-wide parameters',
        'manageStaff' => 'create and modify staff users',
        'manageRoles' => 'manage administrative roles',
        'assignPermissions' => 'assign permissions to roles',
        'assignRoles' => 'assign administrative roles to staff users',
        'manageSimulator' => 'manage simulators',
        'confirmBooking' => 'confirm bookings made on existing timeslots',
        'confirmBookingRequest' => 'confirm requests of booking for timespans out of predefined timeslots',
        'assignInstructors' => 'assign Instructors to the bookings',
        'assignedToBooking' => 'be assigned to a booking as Instructor',
    ];

    public function up()
    {
        $manager=Yii::$app->authManager;
        foreach($this->p as $perm => $pDesc){
            $newP=$manager->createPermission($perm);
            $newP->description=$pDesc;
            $manager->add($newP);
        }
    }

    public function down()
    {
        $manager=Yii::$app->authManager;
        foreach($this->p as $perm => $pDesc){
            $p=$manager->getPermission($perm);
            $manager->remove($p);
        }
    }
}
