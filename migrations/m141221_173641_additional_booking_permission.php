<?php

use yii\db\Schema;
use yii\db\Migration;

class m141221_173641_additional_booking_permission extends Migration
{
    public function up()
    {
        $p=Yii::$app->authManager->createPermission('manageBookings');
        $p->description="view and edit all the existing bookings";
        Yii::$app->authManager->add($p);
    }

    public function down()
    {
        $p=Yii::$app->authManager->getPermission("manageBookings");
        Yii::$app->authManager->remove($p);
    }
}
