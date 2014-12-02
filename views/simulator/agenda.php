<?php

use app\models\Simulator;
use yii\helpers\Html;
use talma\widgets\FullCalendar;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $week string */
/* @var $slots array */
/* @var $simulator integer */
/* @var $currWeek DateTime */
/* @var $prevWeek string */
/* @var $nextWeek string */
/* @var $simulator Simulator */


$this->title = Yii::t('app', "{simulator}'s agenda", [
    'simulator' => $simulator->getAttribute("name")
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Simulators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $simulator->getAttribute("name")];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Agenda')];
?>
<div class="simulator-availability">

    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= \Yii::t('app','Click on a timeslot to make a booking'); ?></p>

    <div id="calendar_buttons">
        <a href="<?= Url::to([
                'simulator/agenda',
                'id' => $simulator->getAttribute("id"),
                'week' => $prevWeek
            ])?>">
            <?= \Yii::t('app',"Previous Week");?>
        </a>&nbsp;
        <a href="<?= Url::to([
                'simulator/agenda',
                'id' => $simulator->getAttribute("id"),
                'week' => $nextWeek

        ])?>">
            <?= \Yii::t('app',"Next Week");?>
        </a>
    </div>

    <?php
    $events = [];
    foreach ($slots as $s) {
        $a = [
            'start' => $s->start,
            'end' => $s->end,
            'id' => $s->id
        ];

        if ($s->id_booking != null) {
            $a['title'] = \Yii::t('app', 'Unavailable');
            $a['className'] = 'unavailable';
        } else {
            $a['title'] = \Yii::t('app', 'Available');
            $a['className'] = 'available';
        }

        $events[] = $a;
    }

    $bookUrl = Url::to(['booking/create','timeslots[]'=>'']);

    echo FullCalendar::widget([
        'config' => [
            'header' => [
                'left' => '',
                'center' => 'title',
                'right' => ''
            ],
            'aspectRatio' => '2',
            'defaultView' => 'agendaWeek',
            'scrollTime' => '08:00:00',
            'editable' => false,
            'selectable' => true,
            'firstDay' => 1,
            'allDaySlot' => false,
            'defaultDate' => $currWeek->format("c"),
            'events' => $events,
            'eventRender' => new \yii\web\JsExpression('function slotBooking(event, element)
    {
        if(element.hasClass("available")){
            element.click(function(ev){
                ev.preventDefault();
                window.location.href="'.$bookUrl.'"+event.id;
            })
        }
        else{
            element.attr("title","'.\Yii::t('app',"This timeslot is already booked. Choose another one, please.").'");
            element.tooltip();
        }

    }')
        ]
    ]); ?>

</div>