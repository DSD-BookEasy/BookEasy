<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Timeslot */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="time-slot-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'start')->textInput() ?>

    <?= $form->field($model, 'end')->textInput() ?>

    <?= $form->field($model, 'cost')->textInput() ?>

    <?= $model->isNewRecord ? $form->field($model, 'id_timeSlotModel')->textInput() : ''?>

    <?= $model->isNewRecord ? $form->field($model, 'id_simulator')->textInput() : ''?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
