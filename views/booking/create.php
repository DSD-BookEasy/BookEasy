<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $entry_fee integer */
/* @var $timeslots \app\models\Timeslot[] */
/* @var $me app\models\Staff */
/* @var $instructors array */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Booking',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bookings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('app', 'You are about to book the following simulator:')?></p>

    <?php
    $flight_price = $timeslots[0]->cost > 0 ? $timeslots[0]->cost : $timeslots[0]->simulator->price_simulation;
    ?>

    <?= Html::ul([
        Yii::t('app', 'Start: {0, date, medium} {0, time, short}', strtotime($timeslots[0]->start)),
        Yii::t('app', 'End: {0, date, medium} {0, time, short}', strtotime($timeslots[0]->end)),
        Yii::t('app', 'Entrance: {0, number, currency}', $entry_fee),
        Yii::t('app', 'Flight Simulation: {0, number, currency}', $flight_price),
        Yii::t('app', 'Total Cost: {0, number, currency}', $entry_fee + $flight_price),
    ]);
    ?>

    <p><?= Yii::t('app', 'Provide the following information to continue.') ?></p>

    <?= $this->render('_form', [
        'model' => $model,
        'showAddress' => false,
        'me' => $me,
        'instructors' => $instructors
    ]) ?>

</div>
