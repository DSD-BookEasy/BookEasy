<?php

use yii\helpers\Html;
use talma\widgets\FullCalendar;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $week string */
/* @var $slots array */

$this->title = Yii::t('app', 'Timeslots');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="timeslot-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    $events=[];
    foreach($slots as $s){
        $events[]=[
            'title'=>\Yii::t('app',"Available"),
            'start'=>$s->start,
            'end'=>$s->end,
            'class'=>'available'
        ];
    }

    echo FullCalendar::widget([
        'config' => [
            'header' => [
                'left' => 'prev,next today',
                'center' => 'title',
                //'right' => 'agendaWeek,agendaDay'
            ],
            'aspectRatio' => '2',
            'defaultView' => 'agendaWeek',
            'scrollTime' => '08:00:00',
            'editable' => 'false',
            'firstDay' => 1,
            'allDaySlot'=>false,
            'defaultDate'=> $week,
            'events' => $events
        ]
    ]); ?>

</div>
