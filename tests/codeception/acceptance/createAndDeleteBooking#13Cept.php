<?php


$I = new AcceptanceTester($scenario);

$I->wantTo("insertBooking");
$I->amOnPage("/index-test.php/");
$I->click("(//a[contains(text(),'Book »')])[2]");
$I->click("//div[@id='w0']/div[2]/div/table/tbody/tr/td/div/div/div[3]/table/tbody/tr/td[8]/div/a[9]/div");
$I->fillField("#booking-name", "marco");
$I->fillField("#booking-surname", "edemanti");
$I->fillField("#booking-email", "marco@mail.it");
$I->wait(1);
$I->click("button.btn.btn-success");
$I->click("button.btn.btn-success");
$I->wait(2);
//  $I->see("div.booking-view > div > ul > li");
$I->see("Entrance Fee: 80 kr","div.booking-view > div > ul > li");

$I->wait(1);
$I->see("Simulator Fee: 874 kr","//div/div/div/ul/li[2]");

$I->wait(1);
$I->see("Total Fee: 954 kr","//div/div/div/ul/li[3]");

$I->wait(1);
$I->see("Start: Jan 18, 2015 2:30 PM","//div/div/div[2]/ul/li");

$I->wait(1);
$I->see("End: Jan 18, 2015 3:00 PM","//div/div/div[2]/ul/li[2]");
$I->click("Confirm");
$I->wait(1);
 $secretKey=$I->grabTextFrom("span.booking_view_secret_key");
$I->wait(6);
$I->click("Search Booking");

$I->fillField("#booking-name", "marco");
$I->wait(1);
$I->fillField("#booking-surname", "edemanti");
$I->wait(1);
$I->fillField("#booking-token", $secretKey);
$I->wait(1);
$I->click("#searchBtn");
$I->wait(1);
$I->click("Delete");
$I->wait(1);
$I->acceptPopup();