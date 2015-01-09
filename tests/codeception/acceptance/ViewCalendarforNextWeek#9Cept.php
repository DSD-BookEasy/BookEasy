<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo("test that calendar view works for next week story #9");

$I->amOnPage(Yii::$app->homeUrl);
$I->see("Please choose the simulator you wish to book or return to our website.", "//p");
$I->click("img[alt=\"Simulator image\"]");

if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$date = new \DateTime('next week');
$dateString = $date->format('m-d-Y');
$I->fillField("#w1", $dateString);

if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->click("//div[@id='calendar_buttons']/a[3]");
if (method_exists($I, 'wait')) {
    $I->wait(5); // only for selenium
}
$I->see("Est Simulator");
$I->seeInCurrentUrl($date->modify('1 week')->format("Y\WW"));