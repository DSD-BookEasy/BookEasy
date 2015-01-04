<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \talma\widgets\FullCalendar;
use \yii\helpers\Url;
use kartik\datetime\DateTimePicker;


/* @var $this yii\web\View */
/* @var $model app\models\Booking */
/* @var $entry_fee integer */
/* @var $simulator app\models\Simulator */
/* @var $timeslots app\models\Timeslot[] */
/* @var $businessHours array */

$this->title = Yii::t('app', 'Request Booking');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bookings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-create">

    <?php
        $totalFee = $entry_fee + $simulator->price_simulation;
    ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('app','You are about to send a request for opening the museum for a special visit and a flight simulation.
    Please fill in the following form to continue')?>:</p>

    <?= Html::ul([
        Yii::t('app','Entrance Fee: {0, number, currency}', $entry_fee),
        Yii::t('app','Flight Simulator slot price: {0, number, currency}', $simulator->price_simulation),
        Yii::t('app','Simulator slot duration: {duration} minutes', ['duration' => $simulator->flight_duration]),
        Yii::t('app','Total Fee: {0, number, currency}', $totalFee),
    ]);
    ?>
    <p><?=Yii::t('app','Please note that the above prices are subject to change. Additional costs may be applied for the museum opening out of usual opening hours. You will receive the final quotation as soon as the staff can confirm your booking.')?></p>

    <?php
    $form = ActiveForm::begin(['action' => ['']]);

    echo $this->render('_form', [
        'model' => $model,
        'showAddress' => true,
        'form' => $form
    ]);
    ?>
    <fieldset>
        <legend><?= Yii::t('app',"Time Span")?></legend>
        <div id="dynamic_fields">
            <?php
                $deleteButton=Html::a('','#',['class' => 'glyphicon glyphicon-remove btn btn-warning dynamic_remove']);
                $first=true;
                foreach($timeslots as $t){
                    $start_end=$form->field($t, 'start', ['options' => ['class' => 'col-md-5']])->widget(DateTimePicker::className(), [
                        'removeButton' => false,
                        'options' => ['placeholder' => Yii::t('app', 'Enter starting time ...')],
                        'pluginOptions' => [
                            'autoclose' => true,
                        ]
                    ]);

                    $start_end.=$form->field($t, 'end', ['options' => ['class' => 'col-md-5']])->widget(DateTimePicker::className(), [
                        'removeButton' => false,
                        'options' => ['placeholder' => Yii::t('app', 'Enter ending time ...')],
                        'pluginOptions' => [
                            'autoclose' => true,
                        ]
                    ]);//TODO make field names usable in array

                    if($first){
                        $first=false;
                        //$start_end.=Html::tag('div','&nbsp;',['class' => 'col-md-2']);
                    }
                    else{
                        $start_end.=Html::tag('div',$deleteButton,['class' => 'col-md-2']);
                    }

                    echo Html::tag('div',
                        $start_end,
                        ['class' => 'dynamic_field row']);
                }
            ?>
            <a href="#" id="dynamic_add" class="btn btn-success"><?= Yii::t('app','Add more Time Spans')?></a>
            <!-- This paragraph just creates some space to the elements below -->
            <p><br></p>
        </div>

    <?php
    echo Html::label(Yii::t('app','Below you can see the availability of the chosen simulator').':');
    echo FullCalendar::widget([
        'config' => [
            'header' => [
                'left' => '',
                'center' => 'title',
            ],
            'aspectRatio' => '2.5',
            'defaultView' => 'agendaWeek',
            'scrollTime' => '08:00:00',
            'editable' => false,
            'firstDay' => 1,
            'allDaySlot' => false,
            'events' => Url::to(['/timeslot/anon-calendar','simulator' => $simulator->id, 'background' => true]),
            'minTime' => $businessHours['start'],
            'maxTime' => $businessHours['end'],
            'eventRender' => new \yii\web\JsExpression('calendarAddTimespan')
        ]
    ]);
    ?>
    </fieldset>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php
    $form->end();
    ?>
</div>
<?php
//Depends clause is necessary to make the js load AFTER jQuery
$this->registerJsFile('@web/js/dynamic_field.js', ['depends' => 'app\assets\AppAsset']);
?>