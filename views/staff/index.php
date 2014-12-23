<?php
/* @var $this yii\web\View */
/* @var $users app\models\ActiveDataProvider */

use yii\grid\GridView;

$this->title = Yii::t('app', 'Staff');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['']];
?>

<h1><?= $this->title ?></h1>

<?= GridView::widget([
    'dataProvider' => $users,
    'columns' => [
        'user_name',
        'name',
        'surname',
        ['class' => 'yii\grid\ActionColumn'],
    ],

]); ?>