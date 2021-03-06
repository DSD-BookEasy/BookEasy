<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TimeslotModel */
/* @var $simulators app\models\Simulator[]*/

$this->title = Yii::t('app', 'Update Recurring Time Slots: ') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Recurring Time Slots'), 'url' => 'index'];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="time-slot-model-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'simulators' => $simulators
    ]) ?>

</div>
