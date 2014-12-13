<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TimeslotModel */
/* @var $weekDays string[] */
/* @var $simulators app\models\Simulator[]*/


$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Time Slot Model',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Time Slot Models'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-slot-model-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'weekDays' => $weekDays,
        'simulators' => $simulators
    ]) ?>

</div>
