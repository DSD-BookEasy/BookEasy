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
            $this->actor->wait(3);
        }
        $this->actor->fillField("#staff-user_name", "admin");
        $this->actor->fillField("#staff-plain_password", "123456789");
        $this->actor->fillField("#staff-repeat_password", "123456789");
        $this->actor->click('Click me once');
        if (method_exists($this->actor, 'wait')) {
            $this->actor->wait(7); // only for selenium
        }
    }
}