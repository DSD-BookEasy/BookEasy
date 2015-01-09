<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $paramsNatures array */

$this->title = Yii::t('app', 'Parameters');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['']];
?>
<div class="parameter-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Yii::t('app',"From here you can change the global parameters of the system")?></p>

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
                'content' => function ($m) use($paramsNatures)
                {
                    return renderFormColumn($m,$paramsNatures);
                },
                'header' => 'Value'
            ],
            'last_update'
        ]
    ]); ?>

</div>
<?php
/**
 * Renders a form specific for the nature of the specified parameter
 * @param $model
 * @param $natures array an array describing which kind of form element to render for each parameter
 * @return string the HTML of the form
 */
function renderFormColumn($model,$natures){
    $name=$model->id;
    if(isset($natures[$name])){
        switch($natures[$name]){
            case 'text':
                return renderFormText($model);
            case 'textarea':
                return renderFormTextarea($model);
            case 'time':
                return renderFormTime($model);
            case 'datetime':
                return renderFormDateTime($model);
            default:
                return renderFormTextarea($model);
        }
    }
    else{
        return renderFormTextarea($model);
    }
}

/**
 * Displays a column with an edit form
 * Callback for GridView
 * @return string
 */
function renderFormTextarea($model){
    ob_start();
    $form = ActiveForm::begin([
      'action' => ['update','id'=>$model->id]
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

function renderFormText($model){
    ob_start();
    $form = ActiveForm::begin([
        'action' => ['update','id'=>$model->id]
    ]);
    $out = ob_get_contents();
    ob_end_clean();

    $out .= $form->field($model, 'value')->textInput()->label(false);
    $out .= Html::submitButton();

    ob_start();
    $form->end();
    $out .= ob_get_contents();
    ob_end_clean();

    return $out;
}

function renderFormDateTime($model){
    ob_start();
    $form = ActiveForm::begin([
        'action' => ['update','id'=>$model->id]
    ]);
    $out = ob_get_contents();
    ob_end_clean();

    $out .= $form->field($model, 'value')->widget(DateTimePicker::className(), [
        'removeButton' => false,
        'pluginOptions' => [
            'autoclose' => true,
        ]
    ]);
    $out .= Html::submitButton();

    ob_start();
    $form->end();
    $out .= ob_get_contents();
    ob_end_clean();

    return $out;
}


function renderFormTime($model){
    ob_start();
    $form = ActiveForm::begin([
        'action' => ['update','id'=>$model->id]
    ]);
    $out = ob_get_contents();
    ob_end_clean();

    $out .= $form->field($model, 'value')->widget(TimePicker::className(), [
        'pluginOptions' => [
            'autoclose' => true,
            'showMeridian' => false,
        ]
    ]);
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