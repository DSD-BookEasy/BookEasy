<?php
/* @var $this yii\web\View */
/* @var $simulators app\models\Simulator[]*/

use yii\helpers\Url;

$this->title = 'Västerås Flygmuseum';
?>
<div class="site-index">

    <div class="body-content">

        <div class="jumbotron">
            <h1><?= Yii::t('app', 'Welcome') ?></h1>

            <p class="lead"><?= Yii::t('app', 'Choose the simulator you wish to book'); ?></p>

        </div>


        <div class="row text-center">

            <?php

            foreach ($simulators as $simulator) {

            ?>
            <div class="col-md-3">
                <h2><?= $simulator->name ?></h2>

                <p><img src="http://placehold.it/225"></p>

                <p><a class="btn btn-default"
                      href="<?= Url::to(['simulator/agenda', 'id' => $simulator->id]); ?>"><?= Yii::t('app',
                            'Book &raquo;'); ?></a></p>
            </div>
            <?php

            }

            ?>
        </div>

    </div>
</div>
