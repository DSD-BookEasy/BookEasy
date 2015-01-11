<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo("test that confirm booking works");

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
$I->see("Logout (admin)");
$I->click("Bookings");
$I->click("Booking List");
$I->see("Waiting for Confirmation", "//div[@id='w0']/table/tbody/tr/td[3]");
$I->click("span.glyphicon.glyphicon-eye-open");
$I->see("Waiting for Confirmation", "td");
$I->click("Confirm");
$I->see("Confirmed", "//div[@id='w0']/table/tbody/tr/td[3]");
$I->click("span.glyphicon.glyphicon-eye-open");
$I->see("Confirmed", "td");

if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
