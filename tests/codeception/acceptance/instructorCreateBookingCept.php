<?php
$I = new AcceptanceTester($scenario);
$I->wantTo("instructorCreateBookingCept");
$I->amOnPage("/index-test.php");
$I->click("Login");

$I->fillField("#staff-user_name", "mercedes26");
$I->fillField("#staff-password", "123456789");
$I->click("#loginBtn");
$I->click("#loginBtn");
$I->wait(1);
$I->click("Bookings");
$I->wait(1);
$I->click("New Booking");
$I->wait(1);
$I->click("(//a[contains(text(),'Book »')])[2]");
$I->wait(1);
if(($I->grabTextFrom("h2"))=="Jan 5 - 11, 2015"){
    $I->click("//div[@id='calendar_buttons']/a[3]");
}
$I->wait(1);

$I->click("//div[@id='w0']/div[2]/div/table/tbody/tr/td/div/div/div[3]/table/tbody/tr/td[8]/div/a/div");
$I->wait(1);

$I->fillField("#booking-name", "marco");
$I->wait(1);
$I->fillField("#booking-surname", "edemanti");
$I->wait(1);
$I->fillField("#booking-email", "a@a.it");
$I->wait(1);
$I->click("button.btn.btn-warning");
$I->wait(1);
$I->see("Create","button.btn.btn-success");
$I->wait(1);
$I->click("button.btn.btn-success");
$I->click("button.btn.btn-success");
$I->wait(1);
$I->click("Confirm");
$I->wait(1);

$I->wait(2);
$I->see("Delete");
$I->click("Delete");
$I->wait(2);
$I->acceptPopup();



