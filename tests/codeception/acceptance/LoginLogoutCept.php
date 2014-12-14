<?php
$I = new AcceptanceTester($scenario);
$I->wantTo("LoginCept");
$I->amOnPage("/index-test.php");
$I->click("Login");
$I->fillField("#staff-user_name", "mercedes26");
$I->fillField("#staff-password", "123456789");
$I->click("#loginBtn");
$I->click("#loginBtn");
$I->see("", "h1");
$I->click("Logout (mercedes26)");
$I->see("", "h1");

