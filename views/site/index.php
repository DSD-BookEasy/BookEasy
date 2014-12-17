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
            <!-- Shows a selection of all available simulators. -->
            <?php

            foreach ($simulators as $simulator) {

            ?>
            <div class="col-md-3">
                <h2><?= $simulator->getAttribute("name") ?></h2>
                <!-- You can book a simulator by either clicking on the picture... -->
                <p><a href="<?= Url::to(['simulator/agenda', 'id' => $simulator->getAttribute("id")]); ?>">
                        <img src="http://placehold.it/225"></a></p>
                <!-- ... or by clicking on the button below the picture. -->
                <p><a class="btn btn-default"
                      href="<?= Url::to(['simulator/agenda', 'id' => $simulator->getAttribute("id")]); ?>"><?= Yii::t('app',
                            'Book &raquo;'); ?></a></p>
            </div>
            <?php

            }

            ?>
        </div>

    </div>
</div>
