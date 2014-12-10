<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that home page works');
$I->amOnPage(Yii::$app->homeUrl);
$I->see('Welcome');
$I->see('Choose the simulator you wish to book');
$I->seeLink('Login');
$I->click('Login');
$I->see('Staff Login');
