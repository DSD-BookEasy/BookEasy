<?php
use app\models\Booking;
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that cancel booking works story #13');

$populatePage = PopulatePage::openBy($I);
$populatePage->populate();

$I->amOnPage(Yii::$app->homeUrl);
$I->see('Choose the simulator you wish to book');
$I->seeLink('Look for a Booking');
$I->click('Look for a Booking');
$I->seeInCurrentUrl('booking/search');
$booking = Booking::find()->one();
$I->fillField("#booking-id", $booking->{'id'});
$I->click("button[type=\"submit\"]");
if (method_exists($I, 'wait')) {
    $I->wait(2); // only for selenium
}
$I->seeInCurrentUrl('booking/'.$booking->{'id'}.'/view');
$I->see($booking->{'name'});
$I->click("//a[contains(text(),'Delete')]");
//specific to selenium
if (method_exists($I, 'acceptPopup')) {
    $I->acceptPopup();
}
$I->amOnPage(Yii::$app->homeUrl);
$I->click('Look for a Booking');
$I->fillField("#booking-id", $booking->{'id'});
$I->click("button[type=\"submit\"]");
if (method_exists($I, 'wait')) {
    $I->wait(2); // only for selenium
}
$I->see('The requested page does not exist');