<?php

use tests\codeception\_pages\LoginPage;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that login works');

$loginPage = LoginPage::openBy($I);

$I->see('Staff Login','h1');
$I->amGoingTo('try to login with empty credentials');
$loginPage->login('', '');
$I->expectTo('see validations errors');
$I->see('Invalid Username or Password','div');

$I->amGoingTo('try to login with wrong credentials');
$loginPage->login('admin', 'wrong');

$I->expectTo('see validations errors');
$I->see('Invalid Username or Password');

$I->amGoingTo('try to login with correct credentials');
$loginPage->login('mercedes26', '123456789');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->expectTo('see user info');
$I->seeLink('Logout (mercedes26)');
$I->click('Logout (mercedes26)');
$I->seeLink('Login');
