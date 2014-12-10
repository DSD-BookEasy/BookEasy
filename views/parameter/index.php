<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Parameters');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parameter-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        //TODO when we have permissions, this button should be rendered only if user can create parameters
        ?>
        <?= Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Parameter',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\Column',
                'content' => 'renderDescriptionColumn',
                'header' => 'Parameter'
            ],
            [
                'class' => 'yii\grid\Column',
                'content' => 'renderFormColumn',
                'header' => 'Value'
            ],
            'last_update'
        ]
    ]); ?>

</div>
<?php
/**
 * Displays a column with an edit form
 * Callback for GridView
 * @return string
 */
function renderFormColumn($model,$key,$index,$column){
    ob_start();
    $form = ActiveForm::begin([
      'action' => ['parameter/update','id'=>$model->id]
    ]);
    $out = ob_get_contents();
    ob_end_clean();

    $out .= $form->field($model, 'value')->textarea(['rows'=>2])->label(false);
    $out .= Html::submitButton();

    ob_start();
    $form->end();
    $out .= ob_get_contents();
    ob_end_clean();

    return $out;
}

/**
 * Displays a column showing both name and description attributes
 * Callback for GridView
 * @return string
 */
function renderDescriptionColumn($model,$key,$index,$column){
    //TODO when we have permissions, this link should be rendered only if user can create parameters
    $content=Html::a($model->name,['view','id'=>$model->id]);
    $out = Html::tag('div',$content,['class'=>'param_name']);
    $out .= Html::tag('div',$model->description,['class'=>'param_description']);
    return $out;
}