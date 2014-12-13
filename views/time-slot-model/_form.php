<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TimeSlotModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="time-slot-model-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'start_time')->textInput() ?>

    <?= $form->field($model, 'end_time')->textInput() ?>

    <?= $form->field($model, 'frequency')->textInput() ?>

    <?= $form->field($model, 'start_validity')->textInput() ?>

    <?= $form->field($model, 'end_validity')->textInput() ?>

    <?= $form->field($model, 'repeat_day')->textInput() ?>

    <?= $form->field($model, 'id_simulator')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
