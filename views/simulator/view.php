<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\simulator */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Simulators'), 'url' => 'index'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simulator-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <div class="row">

        <div class="col-md-3">
            <p class="row">
                <div class="col-md-12">
                    <?php
                    if ($model->getImage()) {
                        echo Html::img('@web/' . $model->getImage()->getPath('250x'),
                            ['alt' => Yii::t('app', 'Simulator image')]);
                    } else {
                        echo Html::img('http://placehold.it/250',
                            ['class' => 'col-md-12', 'alt' => Yii::t('app', 'Simulator image')]);
                    }
                    ?>
                </div>
            </p>
            <p class="row">
                <div class="col-md-6">
                    <?php
                    if ($model->getImage()) {
                        echo Html::img('@web/' . $model->getImage()->getPath('125x'),
                            ['alt' => Yii::t('app', 'Simulator image')]);
                    } else {
                        echo Html::img('http://placehold.it/125',
                            ['alt' => Yii::t('app', 'Simulator image')]);
                    }
                    ?>
                </div>
                <div class="col-md-6">

                    <?php
                    if ($model->getImage()) {
                        echo Html::img('@web/' . $model->getImage()->getPath('70x'),
                            ['alt' => Yii::t('app', 'Simulator image')]);
                    } else {
                        echo Html::img('http://placehold.it/70',
                            ['class' => 'col-md-6', 'alt' => Yii::t('app', 'Simulator image')]);
                    }
                    ?>
                </div>
            </p>
        </div>

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
                <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
