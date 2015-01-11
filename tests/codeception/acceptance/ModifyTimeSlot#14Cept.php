<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo("test that confirm booking works");

$populatePage = PopulatePage::openBy($I);
$populatePage->populate();
//See the simulators on home page
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->amOnPage(Yii::$app->homeUrl);
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see("Please choose the simulator you wish to book or return to our website.", "//p");

$I->click("Login");
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->fillField("#staff-user_name", "admin");
$I->fillField("#staff-password", "123456789");
$I->click("#loginBtn");
$I->click("#loginBtn");
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->click("b.caret");
$I->click("Time Slots");
$I->click("Manage single time slots");
$I->click("span.glyphicon.glyphicon-eye-open");
$I->click("Update");
$I->fillField("#timeslot-end", "2015-01-05 09:20");
$I->click("button.btn.btn-primary");
$I->click("button.btn.btn-primary");
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$oldDate = new \DateTime('previous Monday');
$oldDate->setTime(9,20,0);
$I->see($oldDate->format('Y-m-d H:i:s'), "//tr[3]/td");
$I->click("(//a[contains(text(),'Time Slots')])[2]");
$I->click("span.glyphicon.glyphicon-trash");
if (method_exists($I, 'acceptPopup')) {
    $I->acceptPopup();
    $I->wait(2);
}
$I->see("2", "//div[@id='w0']/table/tbody/tr/td[2]");

