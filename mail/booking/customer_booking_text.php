<?php

use app\models\Booking;
use app\models\Timeslot;
use yii\helpers\Url;

/* @var $booking Booking */
/* @var $totalSimulationCost integer */
/* @var $entryFee integer */
/* @var $timeslots Timeslot[] */
?>

<?= Yii::t('app', 'Hello, {0} \n we have received your request. Below you can find a summary of your booking:', $booking->name) ?>

    <?= Yii::t('app', 'Name') .': '. $booking->name . ' ' . $booking->surname ?>
    <?= Yii::t('app', 'Status') .': '. $booking->status ?>
    <?= Yii::t('app', 'Secret key') .': '. $booking->token ?>
    <?= Yii::t('app', 'Flight cost') .': '. $totalSimulationCost .' kr' ?>
    <?= Yii::t('app', 'Entry fee') .': '. $entryFee .' kr' ?>
    <?= Yii::t('app', 'Total cost') .': '. ($totalSimulationCost + $entryFee) .' kr' ?>


<?= Yii::t('app', 'Booked simulations') ?>

<?php

foreach ($timeslots as $timeslot) {
    $simulator = $timeslot->simulator;
    ?>
    <?= Yii::t('app', 'Simulator') .': '. $simulator->name ?>\n
    <?= Yii::t('app', 'Start') .': '. $timeslot->start ?>\n
    <?= Yii::t('app', 'End') .': '. $timeslot->end ?>\n
    <?= Yii::t('app', 'Cost') .': '. $timeslot->calculateCost() .' kr' ?>\n

    ---------------
<?php
}
?>

<?= Yii::t('app', 'View or cancel your booking on our website: {0}', Url::to(['view', 'id' => $booking->id, 'token' => $booking->token], true)) ?>

