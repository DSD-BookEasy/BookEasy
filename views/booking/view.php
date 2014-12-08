<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $entry_fee integer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bookings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-view">

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

    $price_simulator = 0;
    if (empty($timeslots) == false) {
        foreach ($timeslots as $timeslot) {
            $price_simulator += isset($timeslot->cost) ? $timeslot->cost : 0;
        }
    }

    ?>

    <p>You booked the following simulator:</p>

    <ul>
        <li>Start: <?= $timeslots[0]->start ?></li>
        <li>End: <?= $timeslots[0]->end ?></li>
        <li>Cost: <?= $price_simulator ?> SEK</li>
        <li>Entrance: <?= $entry_fee ?></li>
    </ul>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'status',
            'timestamp',
            'name',
            'surname',
            'telephone',
            'email:email',
            'address',
        ],
    ]) ?>

</div>
