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
/* @var $error string */

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

    <?php
        if(!empty($error)){
            Html::tag('div',$error,['class' => 'alert alert-danger']);
        }
    ?>

    <?= Html::ul([
        Yii::t('app','Entrance Fee: {0, number} kr', $entry_fee),
        Yii::t('app','Simulator Fee: {0, number} kr per {duration} minutes.', [$simulator->price_simulation, 'duration' => $simulator->flight_duration]),
    ]);
    ?>
    <p><?=Yii::t('app','Please note that the above prices are subject to change. Additional costs may be applied for the museum opening out of usual opening hours. You will receive the final quotation as soon as the staff can confirm your booking.')?></p>

    <?php
    $form = ActiveForm::begin(['action' => ['']]);
    echo Html::input('hidden','simulator',$simulator->id);

    echo $this->render('_form', [
        'model' => $model,
        'showAddress' => true,
        'form' => $form,
        'instructors' => $instructors,
        'me' => $me,
    ]);
    ?>
    <fieldset>
        <legend><?= Yii::t('app',"Time Span")?></legend>
        <div id="dynamic_fields">
            <?php
                \kartik\datetime\DateTimePickerAsset::register($this);
                $deleteButton=Html::a('','#',['class' => 'glyphicon glyphicon-remove btn btn-warning dynamic_remove']);
                $i=0;

                foreach($timeslots as $t){
                    $start_end='
<div class="col-md-5 field-timeslot-start">
    <label class="control-label" for="timeslot-start">Start</label>
    <div class="input-group date">
        <span class="input-group-addon" title="Select date &amp; time">
            <span class="glyphicon glyphicon-calendar"></span>
        </span>
        <input type="text" class="form-control picker_input picker_start" name="Timeslot['.$i.'][start]" placeholder="Enter starting time ..." value="'.$t->start.'">
    </div>
    <div class="help-block"></div>
</div>
<div class="col-md-5 field-timeslot-end">
    <label class="control-label" for="timeslot-end">End</label>
    <div class="input-group date">
        <span class="input-group-addon" title="Select date &amp; time">
            <span class="glyphicon glyphicon-calendar"></span>
        </span>
        <input type="text" class="form-control picker_input picker_end" name="Timeslot['.$i.'][end]" placeholder="Enter ending time ..." value="'.$t->end.'">
    </div>
    <div class="help-block"></div>
</div>';
                    if($i!=0){
                        $start_end.=Html::tag('div',$deleteButton,['class' => 'col-md-2']);
                    }

                    echo Html::tag('div',
                        $start_end,
                        ['class' => 'dynamic_field row']);

                    $i++;
                }
            ?>
            <a href="#" id="dynamic_add" class="btn btn-success"><?= Yii::t('app','Add more Time Spans')?></a>
            <!-- This paragraph just creates some space to the elements below -->
            <p><br></p>
        </div>
        <?php
        echo Html::label(Yii::t('app','Below you can see the availability of the chosen simulator. Click on the calendar to add time spans to your booking (time spans in red are not available for booking)').':');

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
                'selectable' => true,
                'select' => new \yii\web\JsExpression('calendarAddTimespan')
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

//Flight time in minutes
$this->registerJs("
var simulationDuration=".$simulator->flight_duration."
",\yii\web\View::POS_HEAD);
?>