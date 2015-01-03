<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Recurring Time Slots');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-slot-model-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <!-- Create new recurring time slot button -->
        <?= Html::a(Yii::t('app', 'Create new'), ['create'], ['class' => 'btn btn-success']) ?>

        <!-- Switch to single time slot management -->
        <?= Html::a(Yii::t('app', 'Manage single time slots'), ['/timeslot/index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_simulator',
            'start_time',
            'end_time',
            'frequency',
            'start_validity',
            // 'end_validity',
            // 'repeat_day',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
