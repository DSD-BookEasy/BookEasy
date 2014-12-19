<?php
/* @var $this yii\web\View */
/* @var $role app\models\AdminRole */

$this->title = Yii::t('app', Yii::t('app','New Role'));
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Permissions'), 'url' => ['permissions/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Administrative Roles'), 'url' => 'roles'];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['add-role', 'name' => $role->name]];

echo $this->render('_form',[
    'role' => $role
]);