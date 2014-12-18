<?php
/* @var $this yii\web\View */
/* @var $simulators app\models\Simulator[]*/

use yii\helpers\Url;

$this->title = 'Västerås Flygmuseum';
?>
<div class="site-index">

    <div class="body-content">
        <div class="jumbotron">
            <h1><?= Yii::t('app', 'Admin control panel') ?></h1>

            <p class="lead"><?= Yii::t('app', 'Choose what you wish to manage'); ?></p>
            <br>
            <p>
                <a class="btn btn-default btn-lg"
                   href="<?= Url::to([
                       'simulator/index',
                   ]); ?>"><?= Yii::t('app',
                        'Simulators'); ?></a>
                <a class="btn btn-default btn-lg"
                   href="<?= Url::to([
                       'booking/index',
                   ]); ?>"><?= Yii::t('app',
                        'Bookings'); ?></a>
                <a class="btn btn-default btn-lg"
                   href="<?= Url::to([
                       'timeslot/index',
                   ]); ?>"><?= Yii::t('app',
                        'Timeslots'); ?></a>
                <a class="btn btn-default btn-lg"
                   href="<?= Url::to([
                       'timeslot-model/index',
                   ]); ?>"><?= Yii::t('app',
                        'Timeslot Models'); ?></a>
                <a class="btn btn-default btn-lg"
                   href="<?= Url::to([
                       'permissions/index',
                   ]); ?>"><?= Yii::t('app',
                        'Permissions Management'); ?></a>
            </p>

        </div>

    </div>
</div>
