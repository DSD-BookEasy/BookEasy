<?php

use kartik\datetime\DateTimePicker;
use kartik\form\ActiveForm;
use kartik\money\MaskMoney;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Timeslot */
/* @var $form yii\widgets\ActiveForm */
/* @var $simulators app\models\Simulator[] */

?>

<div class="time-slot-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'start')->widget(DateTimePicker::className(), [
                'removeButton' => false,
                'options' => ['placeholder' => Yii::t('app', 'Enter starting time ...')],
                'pluginOptions' => [
                    'autoclose' => true,
                ]
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'end')->widget(DateTimePicker::className(), [
                'removeButton' => false,
                'options' => ['placeholder' => Yii::t('app', 'Enter ending time ...')],
                'pluginOptions' => [
                    'autoclose' => true,
                ]
            ]) ?>
        </div>
    </div>

    <?php if ($model->isNewRecord) { ?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'id_simulator')->dropDownList(ArrayHelper::map($simulators, 'id', 'name')) ?>
        </div>
    </div>
    <?php } ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'cost',
                ['addon' => ['prepend' => ['content' => '<i class="glyphicon glyphicon-tag"></i>']]])->widget(MaskMoney::classname(),
                [
                    'pluginOptions' => [
                        'prefix' => '',
                        'suffix' => ' kr',
                        'precision' => 0,
                        'allowNegative' => false
                    ]
                ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'blocking')->checkbox([ 'label' => Yii::t('app', 'Blocked Timeslots (non bookable)')]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
