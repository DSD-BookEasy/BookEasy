<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */


if ( !Yii::$app->user->isGuest && Yii::$app->user->identity->disabled ) {
    // Logout a disabled user and redirect him to the homepage
    Yii::$app->user->logout();
    Yii::$app->controller->redirect(Yii::$app->homeUrl);
}

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

        //Dynamically compose the menu based on user permissions
        $menuItems = [];

        //System menu
        $system = [
            ['label' => Yii::t('app', 'Time Slots'), 'url' => ['timeslot-model/index'], 'permission' => 'manageTimeslotModels'],
            ['label' => Yii::t('app', 'Simulators'), 'url' => ['simulator/index'], 'permission' => 'manageSimulator'],
            ['label' => Yii::t('app', 'Permissions'), 'url' => ['permission/index'], 'permission' => 'assignPermissions'],
            ['label' => Yii::t('app', 'Roles'), 'url' => ['permission/roles'], 'permission' => 'assignRoles'],
            ['label' => Yii::t('app', 'System Parameters'), 'url' => ['parameter/index'], 'permission' => 'manageParams'],
            ['label' => Yii::t('app', 'Staff Accounts'), 'url' => ['staff/index'], 'permission' => 'manageStaff']
        ];
        checkMenuPermissions($system);
        if(!empty($system)){
            $menuItems[] = ['label' => Yii::t('app', 'System'),
                'items' => $system
            ];
        }

        //Booking menu
        $bookings = [
            ['label' => Yii::t('app', 'Todays Bookings'), 'url' => ['staff/agenda'], 'permission' => ['manageBookings', 'assignedToBooking']],
            ['label' => Yii::t('app', 'New Booking'), 'url' => ['site/index'], 'permission' => '@'],
            ['label' => Yii::t('app', 'Booking List'), 'url' => ['booking/index'], 'permission' => 'manageBookings'],
            ['label' => Yii::t('app', 'Search Booking'), 'url' => ['booking/search'], 'permission' => '@']
        ];
        checkMenuPermissions($bookings);
        if(!empty($bookings)){
            $menuItems[] = ['label' => \Yii::t('app', 'Bookings'),
                'items' => $bookings
            ];
        }

        $menuItems[] = ['label' => \Yii::t('app','Search Booking'), 'url' => ['/booking/search']];
        $menuItems[] = Yii::$app->user->isGuest ?
            ['label' => \Yii::t('app','Login'), 'url' => ['/staff/login']] :
            [
                'label' => \Yii::t('app','Logout ({username})',
                    ['username'=>Yii::$app->user->identity->user_name]),
                'url' => ['/staff/logout']
            ];


        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $menuItems
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
<?php $this->endPage();

/**
 * Filters menu entries based on the specified permissions
 * @param $items
 */
function checkMenuPermissions(&$items){
    $toDel = [];
    foreach($items as $key => $entry){
        if(!empty($entry['permission'])){
            if(!is_array($entry['permission'])){
                $entry['permission']=[$entry['permission']];
            }

            $can = false;
            foreach($entry['permission'] as $perm){
                if ($perm === '?') {
                    if (Yii::$app->user->getIsGuest()) {
                        $can = true;
                        break;
                    }
                } elseif ($perm === '@') {
                    if (!Yii::$app->user->getIsGuest()) {
                        $can = true;
                        break;
                    }
                } elseif (Yii::$app->user->can($perm)){
                    $can = true;
                    break;
                }
            }

            if(!$can){
                $toDel[] = $key;
            }
        }
    }

    //Deleting array entries while iterating over it, is not so wise. We do it separately
    foreach($toDel as $k){
        unset($items[$k]);
    }
}
