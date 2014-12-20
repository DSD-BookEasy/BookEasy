<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $role app\models\AdminRole */
?>

<div class="parameter-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($role, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($role, 'description')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton(empty($role->name) ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => empty($role->name) ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
