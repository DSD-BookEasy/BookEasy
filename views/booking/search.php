<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
?>
<h1>booking/search</h1>

<div class="booking-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'surname')->textInput() ?>

    <?= $form->field($model, 'token')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($content = "search")?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

