<?php

use app\models\TimeslotModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\time\TimePicker;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\TimeslotModel */
/* @var $form yii\widgets\ActiveForm */
/* @var $weekDays string[] */
/* @var $simulators app\models\Simulator[] */

?>

<div class="time-slot-model-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'id_simulator')->dropDownList(ArrayHelper::map($simulators, 'id', 'name')) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'start_time')->widget(TimePicker::className(), [
                'pluginOptions' => [
                    'autoclose' => true,
                    'showMeridian' => false,
                ]
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'end_time')->widget(TimePicker::className(), [
                'pluginOptions' => [
                    'autoclose' => true,
                    'showMeridian' => false,
                ]
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'frequency')->dropDownList([
                TimeslotModel::DAILY => Yii::t('app', 'Daily'),
                TimeslotModel::WEEKLY => Yii::t('app', 'Weekly')
            ]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'repeat_day')->dropDownList($weekDays) ?>
        </div>

    </div>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'start_validity')->widget(DatePicker::className(), [
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'convertFormat' => true,
                'pluginOptions' => [
                    'autoclose' => true,
                ]
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'end_validity')->widget(DatePicker::className(), [
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'convertFormat' => true,
                'pluginOptions' => [
                    'autoclose' => true,
                ]
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
