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

<?= Html::tag('p',
    Yii::t('app', 'Hello, {0} <br> we have received your request. Below you can find a summary of your booking:',
        $booking->name)) ?>

<table class="table table-striped">
    <tbody>
    <tr>
        <?= Html::tag('td', Yii::t('app', 'Name')) ?>
        <?= Html::tag('td', $booking->name . ' ' . $booking->surname) ?>
    </tr>
    <tr>
        <?= Html::tag('td', Yii::t('app', 'Status')) ?>
        <?= Html::tag('td', $booking->status) ?>
    </tr>
    <tr>
        <?= Html::tag('td', Yii::t('app', 'Secret key')) ?>
        <?= Html::tag('td', $booking->token) ?>
    </tr>
    <tr>
        <?= Html::tag('td', Yii::t('app', 'Flight cost')) ?>
        <?= Html::tag('td', $totalSimulationCost) ?>
    </tr>
    <tr>
        <?= Html::tag('td', Yii::t('app', 'Entry fee')) ?>
        <?= Html::tag('td', $entryFee) ?>
    </tr>
    <tr>
        <?= Html::tag('td', Yii::t('app', 'Total cost')) ?>
        <?= Html::tag('td', $totalSimulationCost + $entryFee) ?>
    </tr>
    </tbody>
</table>

<h3>Booked simulations:</h3>

<?php

foreach ($timeslots as $timeslot) {
    $simulator = $timeslot->simulator;
    ?>
    <table class="table table-striped">
        <tbody>
        <tr>
            <?= Html::tag('td', Yii::t('app', 'Simulator')) ?>
            <?= Html::tag('td', $simulator->name) ?>
        </tr>
        <tr>
            <?= Html::tag('td', Yii::t('app', 'Start')) ?>
            <?= Html::tag('td', Yii::t('app', $timeslot->start)) ?>
        </tr>
        <tr>
            <?= Html::tag('td', Yii::t('app', 'End')) ?>
            <?= Html::tag('td', Yii::t('app', $timeslot->end)) ?>
        </tr>
        <tr>
            <?= Html::tag('td', Yii::t('app', 'Cost')) ?>
            <?= Html::tag('td', Yii::t('app', $timeslot->calculateCost())) ?>
        </tr>
        </tbody>
    </table>

    <hr>
<?php
}
?>
<p>
    <a class="btn btn-primary" href="<?= Url::to(['booking', 'id' => $booking->id, 'token' => $booking->token], true) ?>"><?= Yii::t('app', 'View or cancel') ?></a><?= Yii::t('app', ' your booking on our website') ?>
</p>
