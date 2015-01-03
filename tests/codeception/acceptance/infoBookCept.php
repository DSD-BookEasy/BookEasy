<?php


$I = new AcceptanceTester($scenario);

$I->wantTo("insertBooking");
        $I->amOnPage("/index-test.php/");
        $I->click("(//a[contains(text(),'Book »')])[2]");
        $I->click("div.fc-content");
        $I->fillField("#booking-name", "marco");
        $I->fillField("#booking-surname", "edemanti");
        $I->fillField("#booking-email", "marco@mail.it");
        $I->click("button.btn.btn-success");
        $I->click("button.btn.btn-success");
        $I->click("Confirm");




