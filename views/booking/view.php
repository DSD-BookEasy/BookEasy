<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $entry_fee integer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bookings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    $timeslots=$model->timeslots;
    ?>

    <p>You booked the following simulator:</p>

    <?php
    $flight_price = $timeslots[0]->cost>0?$timeslots[0]->cost:$timeslots[0]->simulator->price_simulation;
    ?>

    <?= Html::ul([
        Yii::t('app','Start: {0, date, medium} {0, time, short}', strtotime($timeslots[0]->start)),
        Yii::t('app','End: {0, date, medium} {0, time, short}', strtotime($timeslots[0]->end)),
        Yii::t('app','Entrance: {0, number, currency}', $entry_fee),
        Yii::t('app','Flight Simulation: {0, number, currency}', $flight_price),
        Yii::t('app','Total Cost: {0, number, currency}', $entry_fee + $flight_price),
    ]);
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'token',
            'timestamp',
            'name',
            'surname',
            'telephone',
            'email:email',
            'address',
            'comments',
        ],
    ]) ?>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

</div>
