<?php
$I = new AcceptanceTester($scenario);
$I->wantTo("select the next and prev week using button");
$I->amOnPage("/index-test.php");
$I->wait(1);

$date = new \DateTime('next week');
$dateYearString = $date->format('Y');
$dateWeekString = $date->format('W');

$I->click("(//a[contains(text(),'Book »')])[2]");
$I->wait(1);
$currDate=$I->grabTextFrom("h2");

$I->click("//div[@id='calendar_buttons']/a[3]");
$I->wait(2);
$I->seeCurrentUrlEquals("/index-test.php/simulator/2/agenda?week=".$dateYearString."W".$dateWeekString);

$I->wait(1);
$I->click("a.btn.btn-default");
$I->wait(1);
$I->see($currDate, "h2");


