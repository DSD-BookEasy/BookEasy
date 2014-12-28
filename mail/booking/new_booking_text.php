<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
/* @var $id integer the id of the just-created booking */

$mail = Parameter::findOne($id);

echo  $mail->value .
    Url::to(['booking/view','id'=>$id],true)
    ;