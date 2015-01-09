<?php
$I = new AcceptanceTester($scenario);
$I->wantTo("editBooking");
$I->amOnPage("/index-test.php");
$I->click("Login");
$I->fillField("#staff-user_name", "mercedes26");
$I->fillField("#staff-password", "123456789");
$I->wait(2);
$I->click("#loginBtn");
$I->click("#loginBtn");
$I->wait(2);
$I->click("Bookings");
$I->click("Booking List");
$I->wait(1);
$name=$I->grabTextFrom("//div[@id='w0']/table/tbody/tr/td[5]");
$surname=$I->grabTextFrom("//div[@id='w0']/table/tbody/tr/td[6]");
$I->wait(1);
$I->click("span.glyphicon.glyphicon-pencil");
$I->wait(1);
$I->see("Update Booking: $name","h1");
//first assign to an other instructor
$I->fillField("#booking-name", "marco");
$I->fillField("#booking-surname", "edemanti");
$I->fillField("#booking-email", "marco@mail.it");
//then use the button assign to me
$I->wait(1);
$name=$I->grabValueFrom("#booking-name");
$surname=$I->grabValueFrom("#booking-surname");

$I->click("button.btn.btn-primary");
$I->click("button.btn.btn-primary");
$I->wait(1);
$I->see("Booking of $surname $name","h1");




