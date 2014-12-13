<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Time Slot Models');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-slot-model-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Time Slot Model',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'start_time',
            'end_time',
            'frequency',
            'start_validity',
            // 'end_validity',
            // 'repeat_day',
            // 'id_simulator',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
