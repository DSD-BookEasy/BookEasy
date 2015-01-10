<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $entry_fee integer */
/* @var $flight_price int */
/* @var $timeslots \app\models\Timeslot[] */
/* @var $nextTimeslot \app\models\Timeslot */
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

    <?= Html::ul([
        Yii::t('app', 'Start: {0, date, medium} {0, time, short}', strtotime($timeslots[0]->start)),
        Yii::t('app', 'End: {0, date, medium} {0, time, short}', strtotime($timeslots[count($timeslots)-1]->end)),
        Yii::t('app', 'Entrance: {0, number} kr', $entry_fee),
        Yii::t('app', 'Flight Simulation: {0, number} kr', $flight_price),
        Yii::t('app', 'Total Cost: {0, number} kr', $entry_fee + $flight_price),
    ]);
    ?>

    <?php

    if (!empty($nextTimeslot) && !$nextTimeslot->isBooked() && !$nextTimeslot->blocking) {

        echo Html::tag(
            'p',
            Yii::t('app', '{0} your booking until {1,time,short}, or', [
                Html::a(Yii::t('app', 'Extend'), Yii::$app->request->getUrl() . '&timeslots[]=' . $nextTimeslot->id,
                    ['class' => 'btn btn-warning']),
                strtotime($nextTimeslot->end)
            ])
        );
    }
    ?>

    <hr>

    <?= Html::tag('p', Yii::t('app', 'Provide the following information to continue.')) ?>

    <?= $this->render('_form', [
        'model' => $model,
        'showAddress' => false,
        'me' => $me,
        'instructors' => $instructors
    ]) ?>

</div>
