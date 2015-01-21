<?php


$I = new AcceptanceTester($scenario);

$I->wantTo("summarize booking info");
$I->amOnPage("/index-test.php/");
$I->click("(//a[contains(text(),'Book »')])[2]");
$I->wait(1);
    $I->click("//div[@id='calendar_buttons']/a[3]");

$I->wait(1);
$I->click("//div[@id='w0']/div[2]/div/table/tbody/tr/td/div/div/div[3]/table/tbody/tr/td[8]/div/a[9]/div/div[2]");
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
$oldDate = new \DateTime('next Sunday');
$oldDate->add(new DateInterval('P7D'));
$I->wait(1);
$I->see("Start: ".$oldDate->format('M d').", 2015 2:30 PM","div.col-md-3 > ul > li");

$I->wait(1);
$I->see("End: ".$oldDate->format('M d').", 2015 3:00 PM","//div/div[2]/div/ul/li[2]");
$I->see("marco","td");
$I->see("edemanti","tr.booking_view_tab_surname > td");
$I->see("marco@mail.it","tr.booking_view_tab_email > td");

