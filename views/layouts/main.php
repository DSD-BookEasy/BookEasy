<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => 'Västerås Flygmuseum Bookings',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [
                Yii::$app->user->isGuest ?  // if user is guest, show empty label, otherwise show Bookings dropdown
                    ['label' => \Yii::t('app', '')] :
                    ['label' => \Yii::t('app', 'Bookings'),
                        'items' => [
                            ['label' => \Yii::t('app', 'Todays Bookings'), 'url' => ['staff/agenda']],
                            ['label' => \Yii::t('app', 'New Booking'), 'url' => ['site/index']],
                            ['label' => \Yii::t('app', 'Booking List'), 'url' => ['booking/index']],
                            ['label' => \Yii::t('app', 'Search Booking'), 'url' => ['booking/search']]
                        ]
                    ],
                Yii::$app->user->isGuest ?  // if user is guest, show empty label, otherwise show System dropdown
                    ['label' => \Yii::t('app', '')] :
                    ['label' => \Yii::t('app', 'System'),
                        'items' => [
                            ['label' => \Yii::t('app', 'Time Slots'), 'url' => ['timeslot-model/index']],
                            ['label' => \Yii::t('app', 'Simulators'), 'url' => ['simulator/index']],
                            ['label' => \Yii::t('app', 'Permissions'), 'url' => ['permission/index']],
                            ['label' => \Yii::t('app', 'Roles'), 'url' => ['permission/roles']],
                            ['label' => \Yii::t('app', 'System Parameters'), 'url' => ['parameter/index']],
                            ['label' => \Yii::t('app', 'Staff Accounts'), 'url' => ['staff/index']]
                        ]
                    ],


                ['label' => \Yii::t('app','Search Booking'), 'url' => ['/booking/search']],
                Yii::$app->user->isGuest ?
                    ['label' => \Yii::t('app','Login'), 'url' => ['/staff/login']] :
                    [
                        'label' => \Yii::t('app','Logout ({username})',
                        ['username'=>Yii::$app->user->identity->user_name]),
                        'url' => ['/staff/logout']
                    ],

            ],
        ]);
            NavBar::end();
        ?>

        <div class="container controller-<?= Yii::$app->controller->id?> action-<?= Yii::$app->controller->action->id ?>">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; Västerås Flygmuseum <?= date('Y') ?></p>
            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
