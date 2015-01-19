<?php
use \yii\helpers\Url;

/* @var $staff \app\models\Staff */
 ?>

<p style="margin: 0 0 10px;">
    <?= Yii::t('app', 'Hello, {name} {surname}', ['name'=>$staff->name, 'surname' => $staff->surname]) ?>
<br>
    <?= Yii::t('app', 'we have received a password reset request for your account on the museum booking system.') ?>
</p>

<p>
    <?= Yii::t('app', "Click on the following link to reset your password and set a new one:"); ?>
    <br />
    <?= Url::to(['staff/pass-reset','hash' => $staff->recover_hash, 'email' => $staff->email]); ?>
    <br />
    <?= Yii::t('app', "This recovery link is valid only one time and only for the 24 hours after this request."); ?>
</p>

<p>
    <?= Yii::t('app','If you did not request a password reset you can just ignore this email.') ?>
</p>