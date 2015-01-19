<?php
use \yii\helpers\Url;

/* @var $staff \app\models\Staff */
?>
<?= Yii::t('app', 'Hello, {name} {surname}', ['name'=>$staff->name, 'surname' => $staff->surname]);?>

<?= Yii::t('app', 'we have received a password reset request for your account on the museum booking system.') ?>

<?= Yii::t('app', "Click on (or copy and paste in your browser) the following link to reset your password and set a new one:"); ?>
    <?= Url::to(['staff/pass-reset','hash' => $staff->recover_hash, 'id' => $staff->id], true); ?>
<?= Yii::t('app', "This recovery link is valid only one time and only for the 24 hours after this request."); ?>


<?= Yii::t('app','If you did not request a password reset you can just ignore this email.') ?>