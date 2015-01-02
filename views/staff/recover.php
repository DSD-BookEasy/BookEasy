<?php
/*  This view is used for the password recovery. It can be reached via a link on the login page.
 */
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h1><?= Yii::t('app', 'Password Recovery') ?></h1>

<!-- This block handles committed error messages, e.g. "email not found" -->
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

<div class="recover-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

    <?= Html::submitButton(Yii::t('app', 'Send'), ['id' => 'recoverBtn', 'class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>

</div>
