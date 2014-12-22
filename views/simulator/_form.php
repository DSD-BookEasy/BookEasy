<?php

use kartik\file\FileInput;
use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\simulator */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="simulator-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">

        <div class="col-md-4">
            <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
        </div>

    </div>
    <div class="row">

        <div class="col-md-12">
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        </div>

    </div>
    <div class="row">

        <div class="col-md-4">
            <?= $form->field($model, 'flight_duration',
                ['addon' => ['prepend' => ['content' => '<i class="glyphicon glyphicon-time"></i>']]])->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'price_simulation',
                ['addon' => ['prepend' => ['content' => '<i class="glyphicon glyphicon-tag"></i>']]])->textInput(['class' => 'col-md-6']); ?>
        </div>

    </div>
    <div class="row">

        <div class="col-md-4">
            <?= $form->field($model, 'uploadFile')->widget(FileInput::className(), [
                'options' => ['accept' => 'image/*'],
                'pluginOptions' => [
                    'showUpload' => false,
                ],
            ]) ?>
        </div>

    </div>
    <?php if (!$model->isNewRecord) { ?>
    <div class="row">

        <div class="col-md-4">
            <?= Html::checkbox('del_image', false, ['label' => 'Remove Image']) ?>
        </div>

    </div>
    <?php } ?>
    <hr>
    <div class="row">
        <div class="form-group col-md-12">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
