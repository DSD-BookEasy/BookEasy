<?php
$I = new AcceptanceTester($scenario);
$I->wantTo("instructorCreateBookingCept");
$I->amOnPage("/index-test.php");
$I->click("Login");

$I->fillField("#staff-user_name", "mercedes26");
$I->fillField("#staff-password", "123456789");
$I->click("#loginBtn");
$I->click("#loginBtn");
$I->wait(2);

$I->fillField("#w4", "01/11/2015");
$I->wait(2);

$I->see("Available","div.fc-title");
$I->click("div.fc-content");
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
$I->amOnPage("/index-test.php/staff/agenda");
$I->wait(2);
$I->fillField("#w4", "01/11/2015");
$I->wait(2);

$I->click("div.fc-content");
$I->wait(2);
$I->see("Delete");
$I->click("Delete");
$I->wait(2);
$I->acceptPopup();



