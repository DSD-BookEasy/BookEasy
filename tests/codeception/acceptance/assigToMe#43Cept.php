<?php
$I = new AcceptanceTester($scenario);
$I->wantTo("assign a instructor or themself to a booking");
$I->amOnPage("/index-test.php");
$I->click("Login");
$I->fillField("#staff-user_name", "mercedes26");
$I->fillField("#staff-password", "123456789");
$I->wait(2);
$I->click("#loginBtn");
$I->click("#loginBtn");
$I->wait(2);
$I->click("Bookings");
$I->click("Booking List");
$I->wait(1);
$I->click("span.glyphicon.glyphicon-pencil");
$I->wait(2);
//first assign to an other instructor
$I->selectOption("#booking-assigned_instructor", "Nia Howe");
$I->wait(2);
$I->click("button.btn.btn-primary");
$I->click("button.btn.btn-primary");
$I->wait(1);
$I->see("Assigned Instructor","tr.booking_viewForStaff_tab_assigned_instructor_name > th");
$I->see("Nia Howe","tr.booking_viewForStaff_tab_assigned_instructor_name > td");
//then use the button assign to me

$I->wait(2);
$I->click("Bookings");
$I->click("Booking List");
$I->wait(1);
$I->click("span.glyphicon.glyphicon-pencil");
$I->wait(1);
$I->click("button.btn.btn-warning");
$I->wait(1);
$I->click("button.btn.btn-primary");
$I->click("button.btn.btn-primary");
$I->wait(1);
$I->see("Assigned Instructor","tr.booking_viewForStaff_tab_assigned_instructor_name > th");
$I->see("Cedrick Mraz","tr.booking_viewForStaff_tab_assigned_instructor_name > td");
