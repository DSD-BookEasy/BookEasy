<?php

namespace tests\codeception\unit\models;

use yii\codeception\TestCase;

class UserTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        // uncomment the following to load fixtures for user table
        //$this->loadFixtures(['user']);
    }

    public function testMe()
    {
        $staff= \app\models\Staff::findOne(['user_name'=>'mercedes26']);
        if(!empty($staff) and $staff->isValidPassword('123456789')){

        }
        else {

        }
    }
}
