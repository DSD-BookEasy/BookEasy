<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h1><?= Yii::t('app', 'Password Recovery') ?></h1>
<?php
if (!empty($error)) {
    ?>
    <div class="error">
        <?= HTML::encode($error); ?>
    </div>
<?php
}
?>

<div class="recover-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

    <?= Html::submitButton(Yii::t('app', 'Send'), ['id' => 'sendBtn', 'class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>

</div>
