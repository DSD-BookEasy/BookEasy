<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h1><?= Yii::t('app', 'Staff Login') ?></h1>
<?php
if (!empty($error)) {
    ?>
    <div class="error">
        <br>
        <h3><?= HTML::encode($error); ?></h3>
        <br>
    </div>
<?php
}
?>

<div class="login-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= Html::submitButton(Yii::t('app', 'Login'), ['id' => 'loginBtn', 'class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>

    <div>
        <!-- This part creates a simple HTML link to the password recovery site -->
        <br>
        <?= Html::tag('p',Yii::t('app',"{click}",[
            'click' => Html::a(Yii::t('app','Lost password?'),['staff/recover'])
        ]))?>
    </div>

</div>
