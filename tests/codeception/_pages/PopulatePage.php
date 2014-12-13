<?php
/**
 * Created by PhpStorm.
 * User: Mert
 * Date: 9.12.2014
 * Time: 00:32
 */

namespace tests\codeception\_pages;


use yii\codeception\BasePage;

/**
 * This class can be used before each test to repopulate the database
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class PopulatePage extends BasePage
{
    public $route = 'populator/index';

    public function populate()
    {
        $this->actor->click('#clear');
        //specific to selenium
        if (method_exists($this->actor, 'acceptPopup')) {
            $this->actor->acceptPopup();
        }
        $this->actor->click('#execute');
    }
}