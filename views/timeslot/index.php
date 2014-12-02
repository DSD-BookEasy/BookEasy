<?php

use yii\helpers\Html;
use talma\widgets\FullCalendar;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $week string */
/* @var $slots array */
/* @var $simulator integer */

$this->title = Yii::t('app', 'Timeslots');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="timeslot-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= \Yii::t('app','Click on a timeslot to make a booking'); ?></p>
    <?php
    $next=strftime("%Y-%m-%d",strtotime($week)+(7*24*60*60));
    $prev=strftime("%Y-%m-%d",strtotime($week)-(7*24*60*60));
    ?>
    <div id="calendar_buttons">
        <a href="<?=\yii\helpers\Url::to(['timeslot/index','simulator'=>$simulator, 'week'=>$prev])?>">
            <?= \Yii::t('app',"Previous Week");?>
        </a>&nbsp;
        <a href="<?=\yii\helpers\Url::to(['timeslot/index','simulator'=>$simulator, 'week'=>$next])?>">
            <?= \Yii::t('app',"Next Week");?>
        </a>
    </div>

    <?php
    $events=[];
    foreach($slots as $s){
        $a=['start' => $s->start,
            'end' => $s->end,
            'id' => $s->id
        ];

        if($s->id_booking!=null){
            $a['title']=\Yii::t('app','Unavailable');
            $a['className']='unavailable';
        }
        else{
            $a['title']=\Yii::t('app','Available');
            $a['className']='available';
        }

        $events[]=$a;
    }

    $bookUrl=\yii\helpers\Url::to(['booking/create','timeslots[]'=>'']);

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
            'allDaySlot'=> false,
            'defaultDate'=> $week,
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