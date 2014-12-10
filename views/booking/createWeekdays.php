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

    <?= Html::ul([
        Yii::t('app','Start: {0, date, medium} {0, time, short}', strtotime($timeslots[0]->start)),
        Yii::t('app','End: {0, date, medium} {0, time, short}', strtotime($timeslots[0]->end)),
        Yii::t('app','Entrance: {0, number, currency}', $entry_fee),
        Yii::t('app','Flight Simulation: {0, number, currency}', $timeslots[0]->simulator->price_simulation),
        Yii::t('app','Total Cost: {0, number, currency}', $entry_fee + $timeslots[0]->simulator->price_simulation),
        Yii::t('app','Additional costs may be applied for the museum opening out of usual opening hours. You will receive the final price as soon as the staff can confirm your booking.')
    ]);
    ?>

    <p><?=Yii::t('app','Provide the following information to continue') ?></p>

    <?= $this->render('_form', [
        'model' => $model,
        'showAddress' => true
    ]) ?>

</div>
