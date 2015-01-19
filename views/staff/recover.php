<?php
/**
 * This view is used for the password recovery. It can be reached via a link on the login page.
 */

/* @var $this yii\web\View */
/* @var $error string */
/* @var $confirm string */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h1><?= Yii::t('app', 'Password Recovery') ?></h1>

<?php
//This block handles committed error messages, e.g. "email not found"
if (!empty($error)) {
    echo Html::tag('div', $error, ['class' => 'alert alert-warning']);
}

if(!empty($confirm)) {
    echo Html::tag('div', $confirm, ['class' => 'alert alert-success']);
}
?>

<div class="recover-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Html::label(Yii::t('app','Email or User Name'),'identificator'); ?>
    <?= Html::input('text','identificator'); ?>

    <?= Html::submitButton(Yii::t('app', 'Recover'), ['id' => 'recoverBtn', 'class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>

</div>
