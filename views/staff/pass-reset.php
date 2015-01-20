<?php
/* @var $this yii\web\View */
/* @var $staff app\models\Staff */
/* @var $confirm bool */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app','Password Reset');
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1',$this->title);

if($confirm){
    Html::tag('div', Yii::t('app',"Your password has been successfully reset. You can now login."), ['class' => 'alert
    alert-success']);
} else {
    $form = ActiveForm::begin();

    echo $form->field($staff, 'id')->hiddenInput();
    echo $form->field($staff, 'recover_hash')->hiddenInput();
    echo $form->field($staff, 'plain_password')->passwordInput();
    echo $form->field($staff, 'repeat_password')->passwordInput();

    echo Html::submitButton(Yii::t('app', 'Change Password'), ['class' => 'btn btn-primary']);

    $form->end();
}