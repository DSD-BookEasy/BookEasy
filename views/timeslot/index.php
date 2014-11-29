<?php

use yii\helpers\Html;
use talma\widgets\FullCalendar;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Timeslots');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="timeslot-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= FullCalendar::widget([
        'config' => [
            'header' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'agendaWeek,agendaDay'
            ],
            'aspectRatio' => '2',
            'defaultView' => 'agendaWeek',
            'scrollTime' => '08:00:00',
            'editable' => 'false',
            'events' => [
                'url' => '../assets/events.json'
            ]
        ]
    ]); ?>

</div>
