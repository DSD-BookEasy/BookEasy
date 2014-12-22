<?php
/* @var $this yii\web\View */
/* @var $simulators app\models\Simulator[]*/

use yii\helpers\Html;
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
            </p>

        </div>

        <div class="row text-center">

            <?php foreach ($simulators as $simulator) { ?>
                <div class="col-md-3">
                    <h2><?= $simulator->name ?></h2>
                    <p>
                        <?php
                        if ($simulator->getImage()) {
                            echo Html::img('@web/' . $simulator->getImage()->getPath('250x'),
                                ['alt' => Yii::t('app', 'Simulator image')]);
                        } else {
                            echo Html::img('http://placehold.it/250',
                                ['alt' => Yii::t('app', 'Simulator image')]);
                        }
                        ?>
                    </p>
                    <p><a class="btn btn-default"
                          href="<?= Url::to(['simulator/agenda', 'id' => $simulator->id]); ?>"><?= Yii::t('app',
                                'Book &raquo;'); ?></a></p>
                </div>
            <?php } ?>
        </div>

    </div>
</div>
