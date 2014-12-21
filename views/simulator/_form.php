<?php

use kartik\file\FileInput;
use kartik\money\MaskMoney;
use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\simulator */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="simulator-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype'=>'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'flight_duration',
        ['addon' => ['prepend' => ['content' => '<i class="glyphicon glyphicon-time"></i>']]])->textInput() ?>

    <?= $form->field($model, 'price_simulation',
        ['addon' => ['prepend' => ['content' => '<i class="glyphicon glyphicon-tag"></i>']]])->widget(MaskMoney::classname(),
        [
            'pluginOptions' => [
                'prefix' => '',
                'suffix' => ' kr',
                'precision' => 0,
                'allowNegative' => false
            ]
        ]); ?>

    <?= $form->field($model, 'uploadFile')->widget(FileInput::className(), [
        'options' => ['accept' => 'image/*'],
        'pluginOptions' => [
            'showUpload' => false,
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
