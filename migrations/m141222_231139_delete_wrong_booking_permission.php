<?php

use yii\db\Schema;
use yii\db\Migration;

class m141222_231139_delete_wrong_booking_permission extends Migration
{
    public function up()
    {
        $p=Yii::$app->authManager->getPermission('confirmBookingRequest');
        Yii::$app->authManager->remove($p);
    }

    public function down()
    {
        $p=Yii::$app->authManager->createPermission('confirmBookingRequest');
        $p->description="confirm requests of booking for timespans out of predefined timeslots";
        Yii::$app->authManager->add($p);
    }
}
