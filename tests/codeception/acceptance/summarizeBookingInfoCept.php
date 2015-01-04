<?php


$I = new AcceptanceTester($scenario);

$I->wantTo("insertBooking");
$I->amOnPage("/index-test.php/");
$I->click("(//a[contains(text(),'Book »')])[2]");
$I->click("//div[@id='w0']/div[2]/div/table/tbody/tr/td/div/div/div[3]/table/tbody/tr/td[8]/div/a[4]/div");
$I->fillField("#booking-name", "marco");
$I->fillField("#booking-surname", "edemanti");
$I->fillField("#booking-email", "marco@mail.it");
$I->wait(1);
$I->click("button.btn.btn-success");
$I->click("button.btn.btn-success");
$I->wait(5);
//  $I->see("div.booking-view > div > ul > li");
$I->see("Entrance Fee: $80.00","div.booking-view > div > ul > li");

$I->wait(1);
$I->see("Simulator Fee: $874.00","//div/div/div/ul/li[2]");

$I->wait(1);
$I->see("Total Fee: $954.00","//div/div/div/ul/li[3]");

$I->wait(1);
$I->see("Start: Jan 4, 2015 11:30 AM","//div/div/div[2]/ul/li");

$I->wait(1);
$I->see("End: Jan 4, 2015 12:00 PM","//div/div/div[2]/ul/li[2]");