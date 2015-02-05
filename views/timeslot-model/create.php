<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TimeslotModel */
/* @var $simulators app\models\Simulator[]*/


$this->title = Yii::t('app', 'Create Recurring Time Slots');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Recurring Time Slots'), 'url' => 'index'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-slot-model-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'simulators' => $simulators
    ]) ?>

</div>
