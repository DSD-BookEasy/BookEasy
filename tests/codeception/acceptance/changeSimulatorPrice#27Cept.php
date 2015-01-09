<?php
$I = new AcceptanceTester($scenario);
$I->wantTo("changePrice");
$I->amOnPage("/index-test.php");
$I->click("Login");
$I->fillField("#staff-user_name", "admin");
$I->fillField("#staff-password", "123456789");
$I->wait(1);
$I->click("#loginBtn");
$I->click("#loginBtn");
$I->wait(2);
$I->click("System");
$I->click("Simulators");
$I->wait(1);
$I->see("Simulators","h1");
$I->click("span.glyphicon.glyphicon-pencil");
$I->wait(1);
$currentprice=$I->grabValueFrom("#simulator-price_simulation");
$I->fillField("#simulator-price_simulation", $currentprice+100);

$I->click("button.btn.btn-primary");
$I->wait(1);
$I->see($currentprice+100,"//tr[4]/td");

$I->wait(1);

