<?php
/**
 * Basic layout for emails in text format
 */
/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */

echo $content;
echo "\n";
echo \Yii::t('app',"Best Regards,\nVästerås Flygmuseum Staff");