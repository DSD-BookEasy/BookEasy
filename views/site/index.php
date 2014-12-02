<?php
/* @var $this yii\web\View */
use yii\helpers\Url;

$this->title = 'Västerås Flygmuseum';
?>
<div class="site-index">

    <div class="body-content">

        <div class="jumbotron">
            <h1>Welcome!</h1>

            <p class="lead">Choose the simulator you wish to book</p>

        </div>


        <div class="row text-center">
            <div class="col-lg-3">
                <h2>Est Simulator</h2>

                <p><img src="http://placehold.it/225"></p>

                <p><a class="btn btn-default" href="<?= Url::to([
                        'simulator/agenda',
                        'id' => '1',
                    ])?>">Book &raquo;</a></p>
            </div>
            <div class="col-lg-3">
                <h2>Eliquam Simulator</h2>

                <p><img src="http://placehold.it/225"></p>

                <p><a class="btn btn-default" href="<?= Url::to([
                        'simulator/agenda',
                        'id' => '2',
                    ])?>">Book &raquo;</a></p>
            </div>
            <div class="col-lg-3">
                <h2>Voluptas Simulator</h2>

                <p><img src="http://placehold.it/225"></p>

                <p><a class="btn btn-default" href="<?= Url::to([
                        'simulator/agenda',
                        'id' => '3',
                    ])?>">Book &raquo;</a></p>
            </div>
            <div class="col-lg-3">
                <h2>Ab Simulator</h2>

                <p><img src="http://placehold.it/225"></p>

                <p><a class="btn btn-default" href="<?= Url::to([
                        'simulator/agenda',
                        'id' => '4',
                    ])?>">Book &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
