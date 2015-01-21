<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Timeslot */
/* @var $simulators app\models\Simulator[] */

$this->title = Yii::t('app', 'Create Time Slot');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Time Slots'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-slot-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'simulators' => $simulators,
    ]) ?>

</div>
