<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $form yii\widgets\ActiveForm */
/* @var $showAddress boolean */
?>

<div class="booking-form">

    <?php $form = ActiveForm::begin([
        'action' => ['']//Necessary to avoid automatic re-use of GET parameters
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'surname')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'telephone')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

    <?php
    if($showAddress) {
        echo $form->field($model, 'address')->textInput(['maxlength' => 255]);
    }
    ?>

    <?= $form->field($model, 'comments', ['inputOptions' => ['placeholder' => Yii::t('app', '(e.g. preferred instruction language, additional guided tour required?, ...)')]])->textarea(['rows' => 4, 'style'=>'width:100%;']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
