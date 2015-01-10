<?php
$I = new AcceptanceTester($scenario);
$I->wantTo("select the next and prev week using button");
$I->amOnPage("/index-test.php");
$I->wait(1);
$I->click("(//a[contains(text(),'Book »')])[2]");
$I->wait(1);
$I->click("//div[@id='calendar_buttons']/a[3]");
$I->wait(1);
$I->see("Jan 12 - 18, 2015", "h2");
$I->wait(1);
$I->click("a.btn.btn-default");
$I->wait(1);
$I->see("Jan 5 - 11, 2015", "h2");


