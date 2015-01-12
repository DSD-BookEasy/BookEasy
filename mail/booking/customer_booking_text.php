<?php

use app\models\Booking;
use app\models\Timeslot;
use yii\helpers\Url;

/* @var $booking Booking */
/* @var $totalSimulationCost integer */
/* @var $entryFee integer */
/* @var $timeslots Timeslot[] */


echo Yii::t('app', 'Hello, {0}', $booking->name) . "\r\n";
echo Yii::t('app', 'we have received your request. Below you can find a summary of your booking:') . "\r\n\r\n";

echo Yii::t('app', 'Name') . ': ' . $booking->name . ' ' . $booking->surname . "\r\n";
echo Yii::t('app', 'Status') . ': ' . $booking->status . "\r\n";
echo Yii::t('app', 'Secret key') . ': ' . $booking->token . "\r\n";
echo Yii::t('app', 'Flight cost') . ': ' . $totalSimulationCost . ' kr' . "\r\n";
echo Yii::t('app', 'Entry fee') . ': ' . $entryFee . ' kr' . "\r\n";
echo Yii::t('app', 'Total cost') . ': ' . ($totalSimulationCost + $entryFee) . ' kr' . "\r\n";

echo "\r\n\r\n";
echo Yii::t('app', 'Booked simulations') . "\r\n\r\n";

foreach ($timeslots as $timeslot) {
    $simulator = $timeslot->simulator;

    echo Yii::t('app', 'Simulator') . ': ' . $simulator->name . "\r\n";
    echo Yii::t('app', 'Start') . ': ' . $timeslot->start . "\r\n";
    echo Yii::t('app', 'End') . ': ' . $timeslot->end . "\r\n";
    echo Yii::t('app', 'Cost') . ': ' . $timeslot->calculateCost() . ' kr' . "\r\n";

    echo "\r\n";
    echo "---------------\r\n";
    echo "\r\n";
}
echo "\r\n";
echo Yii::t('app', 'View or cancel your booking on our website: {0}',
    Url::to(['view', 'id' => $booking->id, 'token' => $booking->token], true));

