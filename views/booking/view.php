<?php

use yii\helpers\Html;
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
            Yii::t('app', 'Your Secret Key is (save it!!!): ') .
                Html::tag('span', $model->token, ['class' => 'booking_view_secret_key'])
        )

    ?>

    <?php
        $flight_price = 0;
        $timeSlots = $model->timeslots;

        foreach ($timeSlots as $slot) {
            $flight_price += $slot->cost > 0 ? $slot->cost : $slot->simulator->price_simulation;
        }
    ?>

    <?= Html::tag('div',

            Html::tag('h3', 'Your booking cost: ') .

            Html::ul([
            Yii::t('app', 'Entrance: {0, number, currency}', $entry_fee),
            Yii::t('app', 'Total Cost: {0, number, currency}', $entry_fee + $flight_price),
            ])
        );
    ?>

    <?php
        $names = ['token', 'name', 'surname', 'telephone', 'email', 'address', 'comments', 'timestamp'];
        $attributes = [];
        foreach($names as $att){
            if($model[$att] != null){
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

    <?php

        foreach($timeSlots as $slot){
            echo Html::tag('div',

                Html::tag('h3', $slot->simulator->name) .

                Html::ul([
                    Yii::t('app','Start: {0, date, medium} {0, time, short}', strtotime($slot->start)),
                    Yii::t('app','End: {0, date, medium} {0, time, short}', strtotime($slot->end)),
                    Yii::t('app','Flight Simulation: {0, number, currency}', $slot->cost > 0 ? $slot->cost : $slot->simulator->price_simulation)
                ])
            );
        }

    ?>

    <div>
        <?php
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