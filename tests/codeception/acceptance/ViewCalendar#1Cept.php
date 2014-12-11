<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo("test that calendar view works for current week");

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
$date = new \DateTime("now");
$week = $date->format("W");
while ($week != 50) { //TODO: not maintainable
    $I->click('Previous Week');
    $week = ($week - 2)%52 + 1;
}
$I->see("Unavailable", "//div[@id='w0']/div[2]/div/table/tbody/tr/td/div/div/div[3]/table/tbody/tr/td[2]/div/a[2]/div/div[2]");
$I->see("Available", "//div[@id='w0']/div[2]/div/table/tbody/tr/td/div/div/div[3]/table/tbody/tr/td[8]/div/a/div/div[2]");
$I->see("Dec 8 - 14, 2014", "h2");