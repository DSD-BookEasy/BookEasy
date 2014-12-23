<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $timeslots app\models\Timeslot[] */
/* @var $entry_fee integer */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Booking',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bookings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('app','You are about to send a request for opening the museum for a special visit and a flight simulation. Below please find the information about your booking')?>:</p>

    <?php
    $simulatorFee = 0;

    if (empty($timeslots) == false) {
        $timeSlot = $timeslots[0];

        $startDateInSeconds = strtotime($timeSlot->start);
        $endDateInSeconds = strtotime($timeSlot->end);

        // Booked time span in milliseconds
        $timeSpanInMillis = $endDateInSeconds - $startDateInSeconds;

        // Booked simulator
        $bookedSimulator = $timeSlot->simulator;

        // Price for a single time slot of a simulator
        // NOTE: Simulator stores time slot length in minutes
        $initialPricingTimeSpanInSeconds = $bookedSimulator->flight_duration * 60;

        // Total number of booked time slots
        $numberOfBookedTimeSlots = ceil($timeSpanInMillis / $initialPricingTimeSpanInSeconds);

        // Final simulator price
        $simulatorFee = $numberOfBookedTimeSlots * $bookedSimulator->price_simulation;
    }
    ?>

    <?= Html::ul([
        Yii::t('app','Start: {0, date, medium} {0, time, short}', strtotime($timeslots[0]->start)),
        Yii::t('app','End: {0, date, medium} {0, time, short}', strtotime($timeslots[0]->end)),
        Yii::t('app','Entrance: {0, number, currency}', $entry_fee),
        Yii::t('app','Flight Simulation: {0, number, currency}', $simulatorFee),
        Yii::t('app','Total Cost: {0, number, currency}', $entry_fee + $simulatorFee),
        Yii::t('app','Additional costs may be applied for the museum opening out of usual opening hours. You will receive the final price as soon as the staff can confirm your booking.')
    ]);
    ?>

    <p><?=Yii::t('app','Provide the following information to continue') ?></p>

    <?= $this->render('_form', [
        'model' => $model,
        'showAddress' => true
    ]) ?>

</div>
