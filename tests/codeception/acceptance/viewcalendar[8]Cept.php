<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo("deneme");

$populatePage = PopulatePage::openBy($I);
$populatePage->populate();

$I->amOnPage("index.php");
$I->see("Choose the simulator you wish to book", "p.lead");
//to be continued