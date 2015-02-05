<?php
use tests\codeception\_pages\PopulatePage;

$I = new AcceptanceTester($scenario);
$I->wantTo("test that confirm booking works");

$populatePage = PopulatePage::openBy($I);
$populatePage->populate();
//See the simulators on home page
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->amOnPage(Yii::$app->homeUrl);
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see("Please choose the simulator you wish to book or return to our website.", "//p");

$I->click("Login");
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->fillField("#staff-user_name", "admin");
$I->fillField("#staff-password", "123456789");
$I->click("#loginBtn");
$I->click("#loginBtn");
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->click("System");
$I->click("Time Slots");
$I->click("Create new");
$I->selectOption("#timeslotmodel-id_simulator", "Eliquam Simulator");

$I->fillField('#timeslotmodel-start_time', '14:00');
$I->fillField('#timeslotmodel-end_time', '15:00');
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->selectOption("#timeslotmodel-frequency", "Weekly");
$I->selectOption("#timeslotmodel-repeat_day", "Wednesday");
$nextWednesday = new \DateTime('next wednesday');
$nextNextWednesday = clone $nextWednesday;
$nextNextWednesday->add(new \DateInterval('P1W'));
$I->fillField('#timeslotmodel-start_validity', $nextWednesday->format('m/d/Y'));
$I->fillField('#timeslotmodel-end_validity', $nextNextWednesday->format('m/d/Y'));
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->click("button.btn.btn-success");
$I->click("button.btn.btn-success");
if (method_exists($I, 'wait')) {
    $I->wait(2); // only for selenium
}
$I->see("14:00:00", "//tr[2]/td");
$I->see("15:00:00", "//tr[3]/td");
$I->see("Weekly", "//tr[4]/td");
$I->see("Eliquam Simulator", "//tr[8]/td");
$I->see("Wednesday", "//tr[7]/td");
$I->click("Bookings");
$I->click("Todays Bookings");
$I->fillField('#w4', $nextWednesday->format('m/d/Y'));
if (method_exists($I, 'wait')) {
    $I->wait(2); // only for selenium
}
$I->click("Eliquam Simulator");
if (method_exists($I, 'wait')) {
    $I->wait(1); // only for selenium
}
$I->see("Available", "div.fc-title");