<?php


$I = new AcceptanceTester($scenario);

$I->wantTo("insertBooking");
        $I->amOnPage("/index-test.php/");
        $I->click("(//a[contains(text(),'Book »')])[2]");
        $I->click("//div[@id='w0']/div[2]/div/table/tbody/tr/td/div/div/div[3]/table/tbody/tr/td[8]/div/a[6]/div");

        $I->wait(1);
$I->fillField("#booking-name","marco");

        $I->wait(1);
        $I->fillField("#booking-email", "marco@mail.it");
        $I->wait(1);

        $I->wait(1);
        $I->click("button.btn.btn-success");
        $I->click("button.btn.btn-success");
        $I->wait(5);
        $I->see("Surname cannot be blank.","//form[@id='w0']/div[2]/div");







