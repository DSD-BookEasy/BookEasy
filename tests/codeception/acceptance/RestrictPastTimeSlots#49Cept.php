<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo("test that time slots in the past are restricted story #49");

//Set the initial state of the database
$populatePage = PopulatePage::openBy($I);
$populatePage->populate();

$I->amOnPage(Yii::$app->homeUrl);
$I->see("Please choose the simulator you wish to book or return to our website.", "//p");
$date = new \DateTime('now');

$I->click("img[alt=\"Simulator image\"]");
$I->fillField("#w1", $date->format('m/d/Y'));
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see('Est Simulator', '//h1');
$I->see("Unavailable", "div.fc-title");
