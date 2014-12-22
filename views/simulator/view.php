<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\simulator */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Simulators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simulator-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <div class="row">

        <?php
        if ($model->getImage()) {
            echo Html::img('@web/' . $model->getImage()->getPath('300x'),
                ['class' => 'col-md-3', 'alt' => Yii::t('app', 'Simulator image')]);
        } else {
            echo Html::img('http://placehold.it/300',
                ['class' => 'col-md-3', 'alt' => Yii::t('app', 'Simulator image')]);
        }
        ?>

        <div class="col-md-9">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'name',
                    'description:ntext',
                    'flight_duration',
                    'price_simulation',

                ],
            ]) ?>
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
        </div>
    </div>
</div>
