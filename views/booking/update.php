<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $me app\models\Staff */
/* @var $instructors array */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
        'modelClass' => 'Booking',
    ]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bookings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="booking-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    echo $this->render('_form', [
        'model' => $model,
        'showAddress' => true,
        'me' => $me,
        'instructors' => $instructors
    ]);
    ?>
</div>
