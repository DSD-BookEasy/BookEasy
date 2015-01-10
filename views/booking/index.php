<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\BookingSearch;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Bookings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Booking',
]), ['site/index'], ['class' => 'btn btn-success']);
        echo "hallo";
       ?>
    </p>

    <?= /** @var BookingSearch $searchModel */
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'status',
            'assigned_instructor',
            'timestamp',
            'name',
            'surname',
            // 'telephone',
            // 'email:email',
            // 'address',

            ['class' => 'yii\grid\ActionColumn'],
        ],
        //parse the enumeration of status in its string value
        //this is the only way I found to modify the content of a column
        'beforeRow' => function ($model){
            $model->status = $model->statusToString();
        }
    ]); ?>

</div>
