<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo("test that calendar view works for current week story #1");

$populatePage = PopulatePage::openBy($I);
$populatePage->populate();

$I->amOnPage(Yii::$app->homeUrl);
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see("Choose the simulator you wish to book", "p.lead");

$I->click("(//a[contains(text(),'Book »')])[2]");

if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->fillField("#w1", "12/12/2014");

if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see("Unavailable", "//div[@id='w0']/div[2]/div/table/tbody/tr/td/div/div/div[3]/table/tbody/tr/td[2]/div/a[2]/div/div[2]");
$I->see("Available", "//div[@id='w0']/div[2]/div/table/tbody/tr/td/div/div/div[3]/table/tbody/tr/td[8]/div/a/div/div[2]");
$I->see("Dec 8 - 14, 2014", "h2");