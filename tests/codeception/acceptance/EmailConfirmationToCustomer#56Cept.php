<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 1/9/15
 * Time: 5:09 PM
 */

use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo("test that user receives confirmation e-mails #56");

// Cleared old emails from MailCatcher
#$I->resetEmails();

//Set the initial state of the database
$populatePage = PopulatePage::openBy($I);
$populatePage->populate();

$I->amOnPage(Yii::$app->homeUrl);
$I->see("Please choose the simulator you wish to book or return to our website.", "//p");
$I->click("img[alt=\"Simulator image\"]");
$I->click("//div[@id='calendar_buttons']/a[3]");
$I->fillField("#booking-name", "Mert");
$I->fillField("#booking-surname", "Ergun");
$I->fillField("#booking-telephone", "0123456");
$I->fillField("#booking-email", "mert@mail.com");
$I->click("button.btn.btn-success");
$I->click("button.btn.btn-success");
$I->click("Confirm");
#$I->seeEmailCount(2);

