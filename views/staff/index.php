<?php
/* @var $this yii\web\View */
/* @var $users app\models\ActiveDataProvider */

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Staff Accounts');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['']];
?>

<h1><?= $this->title ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create new staff account'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

<?= GridView::widget([
    'dataProvider' => $users,
    'columns' => [
        'user_name',
        'name',
        'surname',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update}',
        ],
    ],
]); ?>