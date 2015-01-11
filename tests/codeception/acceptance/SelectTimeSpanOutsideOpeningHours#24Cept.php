<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo("test that I can also select a time span outside the opening hours #24");

//$populatePage = PopulatePage::openBy($I);
//$populatePage->populate();
//See the simulators on home page
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->amOnPage(Yii::$app->homeUrl);
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see("Please choose the simulator you wish to book or return to our website. INTENTIONALLY FAIL", "//p");

$I->click("img[alt=\"Simulator image\"]");
$I->click("An alternative way to create a booking is by clicking here. (Recommended for mobile devices)");
$I->fillField("#booking-name", "Mert");
$I->fillField("#booking-surname", "Ergun");
$I->fillField("#booking-telephone", "123456789");
$I->fillField("#booking-email", "mert@mail.com");
$I->fillField("#booking-comments", "English please");
$I->fillField("#timeslot-start", "2015-01-14 10:00");
$I->fillField("#timeslot-end", "2015-01-14 10:30");
$I->click("#dynamic_add");
$I->fillField("(//input[@id='timeslot-start'])[2]", "2015-01-14 11:00");
$I->fillField("(//input[@id='timeslot-end'])[2]", "2015-01-14 11:30");
$I->click("button.btn.btn-success");
$I->click("button.btn.btn-success");

if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}