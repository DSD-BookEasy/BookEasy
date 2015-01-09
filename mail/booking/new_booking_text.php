<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
/* @var $id integer the id of the just-created booking */

echo  $mailText . "\n".
    Url::to(['booking/view','id'=>$id],true)
    ;