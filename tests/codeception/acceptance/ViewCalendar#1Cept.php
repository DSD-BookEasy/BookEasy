<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo("test that calendar view works for current week story #1");

$I->amOnPage(Yii::$app->homeUrl);
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see("Please choose the simulator you wish to book or return to our website.", "//p");

$I->click("(//a[contains(text(),'Book »')])[2]");

if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$date = new \DateTime('next week');
$dateString = $date->format('m-d-Y');
$I->fillField("#w1", $dateString);

if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see('Eliquam Simulator', '//h1');
$I->seeInCurrentUrl($date->format("Y\WW"));