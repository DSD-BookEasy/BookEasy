<?php
/* @var $this yii\web\View */
/* @var $staff app\models\Staff */
/* @var $confirm bool */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

if($confirm){
    Html::tag('div', Yii::t('app',"Your password has been successfully reset. You can now login."), ['class' => 'alert
    alert-success']);
} else {
    $form = ActiveForm::begin();

    $form->field($staff, 'id')->hiddenInput();
    $form->field($staff, 'recover_hash')->hiddenInput();
    $form->field($staff, 'plain_password')->passwordInput();
    $form->field($staff, 'repeat_password')->passwordInput();

    Html::submitButton(Yii::t('app', 'Change Password'), ['class' => 'btn btn-primary']);

    $form->end();
}