<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that home page works');

$populatePage = PopulatePage::openBy($I);
$populatePage->populate();

$I->amOnPage(Yii::$app->homeUrl);
$I->see('Welcome');
$I->see('Choose the simulator you wish to book');
$I->seeLink('Login');
$I->click('Login');
$I->see('Staff Login');
