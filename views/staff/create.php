<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Staff */
/* @var $allRoles yii\rbac\Role[] */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Staff',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Staff'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="staff-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'user' => $model,
        'allRoles' => $allRoles,
        'roles' => []
    ]) ?>

</div>