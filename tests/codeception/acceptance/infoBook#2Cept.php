<?php


$I = new AcceptanceTester($scenario);

$I->wantTo("show info of booking");
        $I->amOnPage("/index-test.php/");
        $I->click("(//a[contains(text(),'Book »')])[2]");
        $I->click("//div[@id='w0']/div[2]/div/table/tbody/tr/td/div/div/div[3]/table/tbody/tr/td[8]/div/a[6]/div");


$I->click("button.btn.btn-success");
$I->click("button.btn.btn-success");
$I->wait(1);
$I->see("Name cannot be blank.","div.help-block");



        $I->fillField("#booking-name", "marco");


$I->click("button.btn.btn-success");
$I->click("button.btn.btn-success");
$I->wait(1);
$I->see("Surname cannot be blank.","//form[@id='w0']/div[2]/div");


        $I->fillField("#booking-surname", "edemanti");

$I->click("button.btn.btn-success");
$I->click("button.btn.btn-success");
$I->wait(1);
$I->see("Email cannot be blank.","//form[@id='w0']/div[4]/div");




        $I->fillField("#booking-email", "marco@mail.it");
        $I->wait(1);
        $I->click("button.btn.btn-success");
        $I->click("button.btn.btn-success");
        $I->wait(1);

     
        $I->see("Your booking cost","h3");
        $I->wait(1);






