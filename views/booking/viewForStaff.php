<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\models\Booking;

/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $entry_fee integer */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bookings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-view">


    <?=
    Html::tag('h1',
        Yii::t('app', 'Booking of {0} {1}', [$model->surname, $model->name])
    )
    ?>

    <?php
    $names = ['status','assigned_instructor_name','token','name', 'surname', 'telephone', 'email', 'address', 'comments', 'timestamp'];
    $attributes = [];
    foreach($names as $att){
        if($model[$att] != null){
            array_push($attributes, $att);
        }
    }
    ?>

    <?php
    //prepare the status to be shown
    $bookingToShow = new Booking($model);
    $bookingToShow->status = $bookingToShow->statusToString();
    $bookingToShow->assigned_instructor_name = $model->assigned_instructor_name;
    ?>

    <?= DetailView::widget([
        'model' => $bookingToShow,
        'attributes' => $attributes,
        'template' => function($attribute){
            //this function set a class for each row in the table.
            //the class is 'booking_view_tab_<attribute_name>'
            return "<tr class = 'booking_viewForStaff_tab_" .$attribute['attribute']. "'><th>" .$attribute['label']. "</th><td>" .$attribute['value'] . "</td></tr>";
        }
    ]) ?>

    <?php
    $timeSlots = $model->timeslots;
    foreach($timeSlots as $slot){
        echo Html::tag('div',

            Html::tag('h3', $slot->simulator->name) .

            Html::ul([
                Yii::t('app','Start: {0, date, medium} {0, time, short}', strtotime($slot->start)),
                Yii::t('app','End: {0, date, medium} {0, time, short}', strtotime($slot->end)),
                Yii::t('app','Flight Simulation: {0, number} SEK', $slot->cost > 0 ? $slot->cost : $slot->simulator->price_simulation)
            ]).

            Html::a(Yii::t('app', 'Edit Time Slot'), ['timeslot/update', 'id' => $slot->id, 'goTo' =>
                    Url::to(['booking/view', 'id' => $model->id, 'token' => $model->token], true)],
                    ['class' => 'btn btn-primary']).

            "<br></br>"
        );
    }

    ?>

    <div>
        <?php

        //this page is for staff, so we can always show the update button
        echo Html::a(Yii::t('app', 'Edit Booking'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
        //check if the user have also the permission for confirm booking
        if (Yii::$app->user->can('confirmBooking') && $model->status == Booking::WAITING_FOR_CONFIRMATION){
                echo Html::a(Yii::t('app', 'Confirm'), ['accept', 'id' => $model->id], ['class' => 'btn btn-primary']);
        }

        // Display 'delete' button
        echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]);

        ?>
    </div>

</div>