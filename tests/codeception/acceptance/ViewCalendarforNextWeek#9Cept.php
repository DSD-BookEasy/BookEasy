<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo("test that calendar view works for next week story #9");

$I->amOnPage(Yii::$app->homeUrl);
$I->see("Choose the simulator you wish to book", "p.lead");
$I->click("(//a[contains(text(),'Book »')])[2]");

if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->fillField("#w1", "12/12/2014");

if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see("Dec 8 - 14, 2014", "h2");
$I->seeLink("Next week");
$I->click("Next week");
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see("Dec 15 - 21, 2014", "h2");