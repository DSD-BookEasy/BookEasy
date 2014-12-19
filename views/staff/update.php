<?php

/* @var $this yii\web\View */
/* @var $user app\models\Staff */
/* @var $allRoles yii\rbac\Role[] */
/* @var $roles yii\rbac\Role[] */

$this->title = Yii::t('app', '{userName}\'s Profile', [
    'userName' => $user->user_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Staff'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Yii::t('app',"Edit: {userName}",['userName' => $user->user_name]);?></h1>

<?php
echo $this->render('_form',[
    'user' => $user,
    'allRoles' => $allRoles,
    'roles' => $roles
]);