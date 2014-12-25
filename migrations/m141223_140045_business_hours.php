<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\Parameter;

class m141223_140045_business_hours extends Migration
{
    public function up()
    {
        $p=new Parameter();
        $p->id = 'businessTimeStart';
        $p->name = 'Museum Opening Hour';
        $p->description = 'Insert the usual opening hour of the museum. This is used for reducing the timespan shown in the calendars and for the acceptable hours for custom booking requests.';
        $p->value = '8:00';
        if(!$p->save()){
            throw new \yii\base\ErrorException("Failed saving starting time in database");
        }

        $p=new Parameter();
        $p->id = 'businessTimeEnd';
        $p->name = 'Museum Closing Hour';
        $p->description = 'Insert the usual closing hour of the museum. This is used for reducing the timespan shown in the calendars and for the acceptable hours for custom booking requests.';
        $p->value = '18:00';
        if(!$p->save()){
            throw new \yii\base\ErrorException("Failed saving end time in database");
        }
    }

    public function down()
    {
        $p=Parameter::findOne("businessTimeStart");
        if(!empty($p) and !$p->delete()){
            throw new \yii\base\ErrorException("Failed removing start time in database");
        }

        $p=Parameter::findOne("businessTimeEnd");
        if(!empty($p) and !$p->delete()){
            throw new \yii\base\ErrorException("Failed removing end time in database");
        }
    }
}
