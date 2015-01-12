<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body style="font-family: 'Helvetica Neue','Helvetica',Arial,sans-serif;font-size: 14px;line-height: 1.42857143;color: #333;background-color: #fafafa;">
    <?php $this->beginBody() ?>
    <div style="background-color: #fff;margin: 10px 20px;">
        <div class="panel panel-primary" style="margin-bottom: 20px;background-color: #fff;border: 1px solid transparent;border-radius: 4px;-webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);box-shadow: 0 1px 1px rgba(0,0,0,.05);border-color: #337ab7;">
            <div class="panel-heading" style="padding: 10px 15px;border-bottom: 1px solid transparent;border-top-left-radius: 3px;border-top-right-radius: 3px;color: #fff;background-color: #337ab7;border-color: #337ab7;">
                <?= Html::tag('h1', Yii::t('app', 'Västerås Flygmuseum Bookings'), ['class' => 'panel-title', 'style' => 'margin: .67em 0;font-size: 16px;font-family: inherit;font-weight: 500;line-height: 1.1;color: inherit;margin-top: 0;margin-bottom: 0;']) ?>
            </div>

            <div class="panel-body" style="padding: 15px;"><p style="margin: 0 0 10px;">
                <?= $content ?>
                <p style="margin: 0 0 10px;"><?= \Yii::t('app', "Best Regards, <br>Västerås Flygmuseum Staff") ?></p>
            </div>

        </div>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
