<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $entry_fee integer */
/* @var $simulator_fee integer */
/* @var $timeSlots \app\models\Timeslot[] */
/* @var $flight_price integer */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bookings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-view">

    <?= Html::tag('div',

        Html::tag('h3', 'Your booking cost: ') .

        Html::ul([
            Yii::t('app', 'Entrance Fee: {0, number} kr', $entry_fee),
            Yii::t('app', 'Simulator Fee: {0, number} kr', $flight_price),
            Yii::t('app', 'Total Fee: {0, number} kr', $entry_fee + $flight_price),
        ])
    );
    ?>

    <?php
    $names = null;
    if (Yii::$app->user->can('manageBookings')) {
        $names = ['name', 'surname', 'assigned_instructor_name','telephone', 'email', 'address', 'comments'];
    }
    else {
        $names = ['name', 'surname', 'telephone', 'email', 'address', 'comments'];
    }
    $attributes = [];
    foreach ($names as $att) {
        if ($model[$att] != null) {
            array_push($attributes, $att);
        }
    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
        'template' => function($attribute){
            //this function set a class for each row in the table.
            //the class is 'booking_view_tab_<attribute_name>'
            return "<tr class = 'booking_view_tab_" .$attribute['attribute']. "'><th>" .$attribute['label']. "</th><td>" .$attribute['value'] . "</td></tr>";
        }
    ]) ?>

    <div class="row">

    <?php

    foreach($timeSlots as $slot){
        echo Html::tag('div',

            Html::tag('h3', $slot->simulator->name) .

            Html::ul([
                Yii::t('app','Start: {0, date, medium} {0, time, short}', strtotime($slot->start)),
                Yii::t('app','End: {0, date, medium} {0, time, short}', strtotime($slot->end))
            ]),
            ['class' => 'col-md-3']
        );
    }

    ?>

    </div>


    <?=
    // Display 'confirm' button
        Html::tag('div', Html::a(Yii::t('app', 'Confirm'), ['confirm'], [
            'class' => 'btn btn-danger'
        ]))
    ?>


</div>