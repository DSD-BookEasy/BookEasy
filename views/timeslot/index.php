<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Time Slots');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-slot-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Time Slot',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'start',
            'end',
            'cost',
            'id_timeSlotModel',
            'id_simulator',
            'id_booking',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
