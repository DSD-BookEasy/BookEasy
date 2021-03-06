<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $form yii\widgets\ActiveForm */
/* @var $showAddress boolean */
/* @var $me app\models\Staff */
/* @var $instructors array */

$printEnd = false;
?>

<div class="booking-form">

    <?php
    $url = [''];
    if (!empty($model->id)) {
        $url['id'] = $model->id;
    }

    if (empty($form)) {
        $form = ActiveForm::begin([
            'action' => $url//Necessary to avoid automatic re-use of GET parameters
        ]);
        $printEnd = true;
    } ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'surname')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'telephone')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

    <?php
    if ($showAddress) {
        echo $form->field($model, 'address')->textInput(['maxlength' => 255]);
    }
    if (Yii::$app->user->can('manageBookings')) {
        $options = array();
        $promptString = 'Not Assigned';/*
        if ($model->assigned_instructor_name != null && strlen($model->assigned_instructor_name) > 0) {
            $promptString = $model->assigned_instructor_name;
        }*/
        $options['prompt'] = $promptString;
        //false if the user cannot assign instructors
        if (!Yii::$app->user->can('assignInstructors')) {
            $options['disabled'] = 'true';
        }
        $disabled = 'disabled';
        if (Yii::$app->user->can('assignedToBooking')) {
            $disabled = '';
        }
        echo $form->field($model, 'assigned_instructor')->dropDownList($instructors, $options);
        echo "<button
            $disabled
            type=\"button\"
            class=\"btn btn-warning\"
            onclick=\"$('#booking-assigned_instructor').val($me->id)\">
            <span class=\"glyphicon glyphicon-user\"></span> Assign to me
          </button><br><br>";
    }
    ?>

    <?= $form->field($model, 'comments', ['inputOptions' => ['placeholder' =>
        Yii::t('app', 'E.g. preferred instruction language (if other than Swedish), disability, wish for a guided tour (outside regular opening hours) ...')]])->textarea(['rows' => 4, 'style'=>'width:100%;']) ?>

    <?php
    if ($printEnd) {
        ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end();
    }
    ?>
</div>