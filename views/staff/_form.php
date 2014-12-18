<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $user app\models\Staff */
/* @var $allRoles yii\rbac\Role[] */
/* @var $roles yii\rbac\Role[] */
?>

<div class="staff-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($user); ?>

    <?= $form->field($user, 'user_name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($user, 'plain_password')->passwordInput(['maxlength' => 255]) ?>

    <?= $form->field($user, 'repeat_password')->passwordInput(['maxlength' => 255]) ?>

    <?= $form->field($user, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($user, 'surname')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($user, 'telephone')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($user, 'email')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($user, 'address')->textInput(['maxlength' => 255]) ?>

    <fieldset>
        <legend><?= Yii::t('app',"Administrative Roles");?></legend>
        <?php
            foreach($allRoles as $r){
                echo Html::checkbox("roles[".$r->name."]", array_key_exists($r->name,$roles))."&nbsp;";
                echo Html::label($r->name,"roles[".$r->name."]",['title' => $r->description])."<br />";
            }
        ?>
    </fieldset>

    <div class="form-group">
        <?= Html::submitButton($user->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Update'), ['class' => $user->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
