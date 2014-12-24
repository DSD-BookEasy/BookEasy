<?php
/* @var $this yii\web\View */
/* @var $timeslots app\models\Timeslot[] */
/* @var $background bool */

$out=[];

foreach($timeslots as $t){
    $out[]=[
        'id' => $t->id,
        'title' => $t->id_simulator? Yii::t('app','Unavailable') : Yii::t('app','Available'),
        'allDay' => false,
        'start' => $t->start,
        'end' => $t->end,
        'className' => $t->id_simulator? 'unavailable' : 'available',
        'rendering' => $background? 'background' : null
    ];
}

echo json_encode($out);