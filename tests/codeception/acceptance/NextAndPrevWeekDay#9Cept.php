<?php
$I = new AcceptanceTester($scenario);
$I->wantTo("provaCept");
$I->amOnPage("/index-test.php");
$I->wait(1);
$I->click("(//a[contains(text(),'Book »')])[2]");
$I->wait(1);
$I->click("//div[@id='calendar_buttons']/a[3]");
$I->wait(1);
$I->see("Jan 19 - 25, 2015", "h2");
$I->wait(1);
$I->click("a.btn.btn-default");
$I->wait(1);
$I->see("Jan 12 - 18, 2015", "h2");


