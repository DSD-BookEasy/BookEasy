<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo('test choosing desired simulator story #5');

$populatePage = PopulatePage::openBy($I);
$populatePage->populate();
//See the simulators on home page
if (method_exists($I, 'wait')) {
    $I->wait(2); // only for selenium
}
$I->amOnPage(Yii::$app->homeUrl);
$I->see("Please choose the simulator you wish to book or return to our website.", "//p");
$I->see("Est Simulator", "//h2");
$I->see("Eliquam Simulator", "//div[2]/h2");

//Choose second simulator which is Eliquam Simulator
$I->click("(//a[contains(text(),'Book »')])[2]");
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see("Eliquam Simulator", "//h1");

//Choose another one
$I->amOnPage(Yii::$app->homeUrl);
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->click("(//a[contains(text(),'Book »')])[3]");
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see("Voluptas Simulator", "//h1");
