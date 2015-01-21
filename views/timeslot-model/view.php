<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\TimeslotModel */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Recurring Time Slots'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-slot-model-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php

        $timeSlotToShow = clone $model;
        $timeSlotToShow->frequency = $timeSlotToShow->frequencyToString();
        $timeSlotToShow->id_simulator = $timeSlotToShow->simulatorToString();
        $timeSlotToShow->repeat_day = $timeSlotToShow->repeatDayToString();

    ?>

    <?= DetailView::widget([
        'model' => $timeSlotToShow,
        'attributes' => [
            'id',
            'start_time',
            'end_time',
            'frequency',
            'start_validity:date',
            'end_validity:date',
            'repeat_day',
            'id_simulator',
            'blocking:boolean'
        ],
    ]) ?>

</div>
