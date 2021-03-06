<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
?>
<h1>Search for a booking in our database</h1>

<div class="booking-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'surname')->textInput() ?>

    <?php
            // Secret Key is not required for searching when can manageBookings, but without it only a random booking will be found
            // if(!Yii::$app->user->can('manageBookings')) {
                echo $form->field($model, 'token')->textInput();
           // };
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['id' => 'searchBtn', 'class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

