<?php
$I = new AcceptanceTester($scenario);
$I->wantTo("pick a date from datepicker");
$I->amOnPage("/index-test.php");
$I->click("(//a[contains(text(),'Book »')])[2]");
$I->wait(2);
$I->fillField("#w1", "01/20/2015");
$I->wait(2);
$I->see("Jan 19 - 25, 2015", "h2");
$I->wait(2);

