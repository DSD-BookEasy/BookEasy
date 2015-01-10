<?php


$I = new AcceptanceTester($scenario);

$I->wantTo("validate input info of booking");
        $I->amOnPage("/index-test.php/");
        $I->click("(//a[contains(text(),'Book »')])[2]");
$I->wait(1);
if(($I->grabTextFrom("h2"))=="Jan 5 - 11, 2015"){
    $I->click("//div[@id='calendar_buttons']/a[3]");
}
        $I->click("//div[@id='w0']/div[2]/div/table/tbody/tr/td/div/div/div[3]/table/tbody/tr/td[8]/div/a[6]/div");



$I->wait(1);


        $I->fillField("#booking-name", "marco");
        $I->fillField("#booking-surname", "edemanti");
        $I->fillField("#booking-email", "marail.it");

    $I->click("button.btn.btn-success");
        $I->click("button.btn.btn-success");
        $I->wait(1);
        $I->see("Email is not a valid email address","//form[@id='w0']/div[4]/div");



         $I->fillField("#booking-telephone", "222q");
        $I->fillField("#booking-email", "marco@mail.it");

$I->click("button.btn.btn-success");
$I->click("button.btn.btn-success");
        $I->wait(1);

$I->see("The phone number must contain only number","//form[@id='w0']/div[3]/div");
        $I->wait(1);
$I->fillField("#booking-telephone", "3482758814");
$I->click("button.btn.btn-success");
$I->click("button.btn.btn-success");
$I->wait(1);
        $I->see("Your booking cost","h3");
        $I->wait(1);






