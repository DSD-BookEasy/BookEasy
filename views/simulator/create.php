<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\simulator */

$this->title = Yii::t('app', 'Add new {modelClass}', [
    'modelClass' => 'simulator',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Simulators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simulator-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
