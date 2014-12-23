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
                <?php
                    $managements=[
                        [
                            'url' => '/simulator/index',
                            'title' => 'Simulators',
                            'permission' => 'manageSimulator'
                        ],
                        [
                            'url' => '/booking/index',
                            'title' => 'Bookings',
                            'permission' => 'manageBookings'
                        ],
                        [
                            'url' => '/timeslot/index',
                            'title' => 'Timeslots',
                            'permission' => 'manageTimeslots'
                        ],
                        [
                            'url' => '/timeslot-model/index',
                            'title' => 'Timeslot Models',
                            'permission' => 'manageTimeslotModels'
                        ],
                        [
                            'url' => '/staff/index',
                            'title' => 'Staff',
                            'permission' => 'manageStaff'
                        ],
                        [
                            'url' => '/permission/index',
                            'title' => 'Permissions Management',
                            'permission' => 'assignPermissions'
                        ],
                        [
                            'url' => '/permission/roles',
                            'title' => 'Administrative Roles Management',
                            'permission' => 'manageRoles'
                        ],
                        [
                            'url' => '/parameter/index',
                            'title' => 'System Parameters Management',
                            'permission' => 'manageParams'
                        ],
                    ];

                    foreach($managements as $m){
                        echo Html::a(Yii::t('app',$m['title']),$m['url'],['class'=> 'btn btn-default btn-lg']);
                    }
                ?>
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
                          href="<?= Url::to(['/simulator/agenda', 'id' => $simulator->id]); ?>"><?= Yii::t('app',
                                'Book &raquo;'); ?></a></p>
                </div>
            <?php } ?>
        </div>

    </div>
</div>
