<?php
/* @var $this yii\web\View */
/* @var $role app\models\AdminRole */

use \yii\widgets\ActiveForm;
use \yii\helpers\Html;

$this->title = Yii::t('app', Yii::t('app','Delete Role'));
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Permissions'), 'url' => ['permissions/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Administrative Roles'), 'url' => 'roles'];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['delete-role', 'name' => $role->name]];

$form = ActiveForm::begin();

echo Html::tag('p',Yii::t('app',"Are you sure you want to delete the role {0}? This action cannot be undone.",$role->name));
?>
<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Yes'), ['class' => 'btn btn-danger']) ?>
    <?= Html::a(Yii::t('app','No'),'permissions/roles',['class' => 'btn btn-default']); ?>
</div>
<?php

$form->end();