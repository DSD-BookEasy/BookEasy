<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \talma\widgets\FullCalendar;
use \yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $entry_fee integer */
/* @var $simulator app\models\Simulator */
/* @var $businessHours array */

$this->title = Yii::t('app', 'Request Special Booking');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bookings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-create">

    <?php
        $totalFee = $entry_fee + $simulator->price_simulation;
    ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('app','You are about to send a request for opening the museum for a special visit and a flight simulation. Below please fill the information about your booking')?>:</p>

    <?= Html::ul([
        Yii::t('app','Entrance Fee: {0, number, currency}', $entry_fee),
        Yii::t('app','Flight Simulator Fee: {0, number, currency}', $simulator->price_simulation),
        Yii::t('app','Total Fee: {0, number, currency}', $totalFee),
    ]);
    ?>
    <p><?=Yii::t('app','Additional costs may be applied for the museum opening out of usual opening hours. You will receive the final quotation as soon as the staff can confirm your booking.')?></p>

    <?php
    $form = ActiveForm::begin(['action' => ['']]);

    echo $this->render('_form', [
        'model' => $model,
        'showAddress' => true,
        'form' => $form
    ]);

    echo Html::label(Yii::t('app','Below you can see the availability of the simulator').':');
    echo FullCalendar::widget([
        'config' => [
            'header' => [
                'left' => '',
                'center' => 'title',
            ],
            'aspectRatio' => '2.5',
            'defaultView' => 'agendaWeek',
            'scrollTime' => '08:00:00',
            'editable' => false,
            'firstDay' => 1,
            'allDaySlot' => false,
            'events' => Url::to(['/timeslot/anon-calendar','simulator' => $simulator->id, 'background' => true]),
            'minTime' => $businessHours['start'],
            'maxTime' => $businessHours['end']
        ]
    ]);
    ?>
    <br /><br />
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php
    $form->end();
    ?>
</div>