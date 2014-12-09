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
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class PopulatePage extends BasePage
{
    public $route = 'populator/index';

    public function populate()
    {
        $this->actor->click('#clear');
        $this->actor->click('#execute');
    }
}