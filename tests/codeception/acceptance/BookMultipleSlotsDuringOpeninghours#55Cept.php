<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo("test that I can book multiple timeslots during opening hours #55");

//Set the initial state of the database
$populatePage = PopulatePage::openBy($I);
$populatePage->populate();

$I->amOnPage(Yii::$app->homeUrl);
$I->see("Please choose the simulator you wish to book or return to our website.", "//p");
$date = new \DateTime('next week');
$I->click("img[alt=\"Simulator image\"]");
$I->fillField("#w1", $date->format('m/d/Y'));
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->amOnPage("/index-test.php/booking/create?timeslots%5B%5D=24");
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->click("Extend");
$I->click("Extend");
$I->fillField("#booking-name", "Mert");
$I->fillField("#booking-surname", "Ergun");
$I->fillField("#booking-telephone", "123456789");
$I->fillField("#booking-email", "mert@mail.com");
$I->fillField("#booking-comments", "English please");
$I->click("button.btn.btn-success");
$I->click("button.btn.btn-success");
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see("Mert", "td");
$I->see("Ergun", "tr.booking_view_tab_surname > td");
$I->see("123456789", "tr.booking_view_tab_telephone > td");
$I->see("mert@mail.com", "tr.booking_view_tab_email > td");
$I->see("English please", "tr.booking_view_tab_comments > td");
$I->see("Est Simulator", "div.col-md-3 > h3");
$I->see("Your booking cost:", "h3");
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->click("Confirm");
$I->see("Confirmed", "tr.booking_view_tab_status > td");
$I->see("Your booking cost:", "h3");

