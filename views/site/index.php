<?php
/* @var $this yii\web\View */
/* @var $simulators app\models\Simulator[]*/

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Västerås Flygmuseum');
?>
<div class="site-index">

    <div class="body-content">

        <div class="jumbotron">
            <h3><?= Yii::t('app', 'Welcome to the Västerås Flygmuseum booking system!') ?></h3>

            <p><?= Yii::t('app', 'Please choose the simulator you wish to book or {click}.',[
                    'click' => Html::a(Yii::t('app','return to our website'),('http://www.flygmuseum.com/'))]); ?>
            </p>

        </div>


        <div class="row text-center">
            <!-- Shows a selection of all available simulators. -->
            <?php

            foreach ($simulators as $simulator) {

            ?>
            <div class="col-md-3">
                <h2><?= $simulator->name ?></h2>
                <!-- Create a click able picture linked to the corresponding simulators agenda. -->
                <p><a href="<?= Url::to(['/simulator/agenda', 'id' => $simulator->id]);?>">
                    <?php
                    if ($simulator->getImage()) {
                        echo Html::img('@web/' . $simulator->getImage()->getPath('250x'),
                            ['alt' => Yii::t('app', 'Simulator image')]);
                    } else {
                        echo Html::img('http://placehold.it/250',
                            ['alt' => Yii::t('app', 'Simulator image')]);
                    }
                    ?>
                </a></p>
                <!-- Following paragraph creates a button, as additional option to the click able pictures above. -->
                <p><a class="btn btn-default"
                      href="<?= Url::to(['/simulator/agenda', 'id' => $simulator->id]); ?>"><?= Yii::t('app',
                            'Book &raquo;'); ?></a>
                </p>
            </div>
            <?php
            }
            ?>
        </div>

    </div>
</div>
