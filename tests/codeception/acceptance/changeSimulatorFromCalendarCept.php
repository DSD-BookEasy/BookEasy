<?php


$I = new AcceptanceTester($scenario);

$I->wantTo("insertBooking");
$I->amOnPage("/index-test.php/");
$I->click("(//a[contains(text(),'Book »')])[2]");
$I->wait(2);
$I->click("img[alt='Simulator image']");
$I->wait(2);
$I->see("Est Simulator","h1");
$I->click("(//img[@alt='Simulator image'])[3]");
$I->wait(2);
$I->see("Voluptas Simulator","h1");
$I->click("(//img[@alt='Simulator image'])[4]");
$I->wait(2);
$I->see("Ab Simulator","h1");
$I->click("(//img[@alt='Simulator image'])[2]");
$I->wait(2);
$I->see("Eliquam Simulator","h1");