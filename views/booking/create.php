<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $entry_fee integer */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Booking',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bookings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>You are about to book the following simulator:</p>

    <?php

    $price_simulator = 0;
    if (empty($timeslots) == false) {
        foreach ($timeslots as $timeslot) {
            $price_simulator += isset($timeslot->cost) ? $timeslot->cost : 0;
        }
    }

    ?>

    <ul>
        <li>Start: <?= $timeslots[0]->start ?></li>
        <li>End: <?= $timeslots[0]->end ?></li>
        <li>Cost: <?= $price_simulator ?> SEK</li>
        <li>Entrance: <?= (int)$entry_fee ?> SEK</li>
    </ul>

    <p>Please provide the following information to continue.</p>

    <?= $this->render('_form', [
        'model' => $model,
        'timeslots' => $timeslots,
    ]) ?>

</div>
