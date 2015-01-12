<?php

use app\models\Booking;
use app\models\Timeslot;
use yii\helpers\Url;

/* @var $booking Booking */
/* @var $totalSimulationCost integer */
/* @var $entryFee integer */
/* @var $timeslots Timeslot[] */
?>

<?= Yii::t('app', 'Hello, {0} we have received your request. Below you can find a summary of your booking:', $booking->name) ?>

    <?= Yii::t('app', 'Name') .': '. $booking->name . ' ' . $booking->surname ?>
    <?= "\r\n" ?>
    <?= Yii::t('app', 'Status') .': '. $booking->status ?>
    <?= "\r\n" ?>
    <?= Yii::t('app', 'Secret key') .': '. $booking->token ?>
    <?= "\r\n" ?>
    <?= Yii::t('app', 'Flight cost') .': '. $totalSimulationCost .' kr' ?>
    <?= "\r\n" ?>
    <?= Yii::t('app', 'Entry fee') .': '. $entryFee .' kr' ?>
    <?= "\r\n" ?>
    <?= Yii::t('app', 'Total cost') .': '. ($totalSimulationCost + $entryFee) .' kr' ?>

<?= "\r\n\r\n\r\n" ?>
<?= Yii::t('app', 'Booked simulations') ?>
<?= "\r\n\r\n\r\n" ?>

<?php

foreach ($timeslots as $timeslot) {
    $simulator = $timeslot->simulator;
    ?>
    <?= Yii::t('app', 'Simulator') .': '. $simulator->name ?>
    <?= "\r\n" ?>
    <?= Yii::t('app', 'Start') .': '. $timeslot->start ?>
    <?= "\r\n" ?>
    <?= Yii::t('app', 'End') .': '. $timeslot->end ?>
    <?= "\r\n" ?>
    <?= Yii::t('app', 'Cost') .': '. $timeslot->calculateCost() .' kr' ?>
    <?= "\r\n\r\n" ?>
    ---------------
    <?= "\r\n\r\n" ?>
<?php
}
?>
<?= "\r\n\r\n" ?>
<?= Yii::t('app', 'View or cancel your booking on our website: {0}', Url::to(['view', 'id' => $booking->id, 'token' => $booking->token], true)) ?>

