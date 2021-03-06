<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Staff */
/* @var $roles yii\rbac\Role[] */


$this->title = $model->user_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Staff'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="staff-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_name',
            'name',
            'surname',
            'telephone',
            'email',
            'address',
            'disabled:boolean'
        ],
    ]) ?>

    <h2><?= Yii::t('app', 'Assigned roles') ?></h2>

    <ul>
    <?php foreach ($roles as $role) { ?>
        <li><strong><?= Html::encode($role->name) ?>: </strong><?= Html::encode($role->description) ?></li>
    <?php } ?>
    </ul>
</div>
