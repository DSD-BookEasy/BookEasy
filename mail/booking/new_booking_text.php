<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
/* @var $id integer the id of the just-created booking */
?>
Hello,
a new booking for the museum simulators has been received.
Check it out here:
<?= Url::to(['booking/view','id'=>$id],true); ?>