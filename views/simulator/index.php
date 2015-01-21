<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Simulators');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simulator-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Add Simulator'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],
            //'id',

            'name',
            'description:ntext',
            'flight_duration',
            'price_simulation',

           ['class' => 'yii\grid\ActionColumn'],
        ],

    ]); ?>
    <? echo ""?>


</div>
