<?php
$I = new AcceptanceTester($scenario);
$I->wantTo("createNewAccount#17Cept");
$I->amOnPage("/index-test.php");
$I->click("Login");
$I->fillField("#staff-user_name", "admin");
$I->fillField("#staff-password", "123456789");
$I->wait(1);
$I->click("#loginBtn");
$I->click("#loginBtn");
$I->wait(1);
$I->click("System");
$I->click("Staff Accounts");
$I->wait(1);
$I->click("Create new staff account");
$I->wait(1);
$I->see("Create Staff","h1");
$I->fillField("#staff-user_name", "marco");
$I->fillField("#staff-plain_password", "1234");
$I->fillField("#staff-repeat_password", "1234");
$I->fillField("#staff-name", "marco");
$I->fillField("#staff-surname", "edemanti");
$I->fillField("#staff-telephone", "3482758814");
$I->fillField("#staff-email", "marco@mail.it");
$I->fillField("#staff-address", "rossi");

$I->wait(1);
$I->click("button.btn.btn-success");

$I->wait(1);
$I->see("marco","h1");
$I->see("marco","//tr[3]/td");
$I->see("edemanti","//tr[4]/td");
$I->see("3482758814","//tr[5]/td");
$I->see("marco@mail.it","//tr[6]/td");
$I->wait(1);
$I->click("Delete");


$I->acceptPopup();


