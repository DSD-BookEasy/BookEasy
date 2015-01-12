<?php

use app\models\Booking;
use app\models\Timeslot;
use yii\helpers\Html;

/* @var $booking Booking */
/* @var $timeslots Timeslot[] */
Yii::warning('2');


?>
<div id="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?= Html::tag('h1', Yii::t('app', 'Västerås Flygmuseum Bookings'), ['class' => 'panel-title']) ?>
        </div>

        <div class="panel-body">
            <?= Html::tag('p', Yii::t('app', 'Hello, {0} <br> we have received your request. Below you can find a summary of your booking:', $booking->name)) ?>

            <table class="table table-striped">
                <tbody>
                    <tr>
                        <?= Html::tag('td', Yii::t('app', 'Name')) ?>
                        <?= Html::tag('td', $booking->name .' '. $booking->surname) ?>
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
                        <?= Html::tag('td', Yii::t('app', 'Total cost (entry fees included)')) ?>
                    </tr>
                </tbody>
            </table>

            <h3>Booked simulations:</h3>

            <?php

            Yii:: warning('fine until now');

            foreach($timeslots as $timeslot) {
                //$simulator = $timeslot->getSimulator();
            ?>
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <?= Html::tag('td', Yii::t('app', 'Simulator')) ?>
                        <?php //Html::tag('td', $simulator->name) ?>
                    </tr>
                    <tr>
                        <?= Html::tag('td', Yii::t('app', 'Start')) ?>
                        <?= Html::tag('td', Yii::t('app', $timeslot->start)) ?>
                    </tr>
                    <tr>
                        <?= Html::tag('td', Yii::t('app', 'End')) ?>
                        <?= Html::tag('td', Yii::t('app', $timeslot->end)) ?>
                    </tr>
                    </tbody>
                </table>

                <hr>
            <?php
            }

            Yii:: warning('fine until now');

            ?>

            <?= Html::tag('p', Yii::t('app', '{0} your booking on our website',
                Html::a(Yii::t('app', 'View or cancel'), ['booking', 'id' => $booking->id, 'token' => $booking->token],
                    ['class' => 'btn btn-primary']))) ?>

        </div>

    </div>
</div>