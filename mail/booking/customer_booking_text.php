<?php

use app\models\Booking;
use app\models\Timeslot;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $booking Booking */
/* @var $totalSimulationCost integer */
/* @var $entryFee integer */
/* @var $timeslots Timeslot[] */
?>

<?= Yii::t('app', 'Hello, {0} \n we have received your request. Below you can find a summary of your booking:\n\n', $booking->name) ?>


        <?= Yii::t('app', 'Name') .': '. $booking->name . ' ' . $booking->surname .'\n' ?>

        <?= Yii::t('app', 'Status') .': '. $booking->status .'\n' ?>

        <?= Yii::t('app', 'Secret key') .': '. $booking->token .'\n' ?>

        <?= Yii::t('app', 'Flight cost') .': '. $totalSimulationCost .' kr\n' ?>

        <?= Yii::t('app', 'Entry fee') .': '. $entryFee .' kr\n' ?>

        <?= Yii::t('app', 'Total cost') .': '. ($totalSimulationCost + $entryFee) .' kr\n' ?>

        \n\n\n

        <?= Yii::t('app', 'Booked simulations') ?>\n\n

<?php

foreach ($timeslots as $timeslot) {
    $simulator = $timeslot->simulator;
    ?>
            <?= Yii::t('app', 'Simulator') .': '. $simulator->name ?>\n

            <?= Yii::t('app', 'Start') .': '. $timeslot->start ?>\n

            <?= Yii::t('app', 'End') .': '. $timeslot->end ?>\n

            <?= Yii::t('app', 'Cost') .': '. $timeslot->calculateCost() .' kr' ?>\n

    \n---------------\n
<?php
}
?>
\n\n
    <?= Yii::t('app', 'View or cancel your booking on our website: {0}', Url::to(['view', 'id' => $booking->id, 'token' => $booking->token], true)) ?>
\n
