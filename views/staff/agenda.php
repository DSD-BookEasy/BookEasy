<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use talma\widgets\FullCalendar;
use yii\helpers\Url;
use app\models\Simulator;


/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\DatePicker */
/* @var $simulators array */
/* @var $currDay DateTime */
/* @var $prevDay string */
/* @var $nextDay string */

$this->title = Yii::t('app', "Todo: title");
?>

<div id="calendar_buttons">
    <a href="<?= Url::to([
        'staff/agenda',
        'day' => $prevDay
    ]) ?>" class="btn btn-default">
        <span class="glyphicon glyphicon-chevron-left"></span>
        <?= \Yii::t('app', "Previous day"); ?>
    </a>

    <a href="<?= Url::to([
        'staff/agenda',
    ]) ?>" class="btn btn-default">
        <?= \Yii::t('app', "Today"); ?>
    </a>

    <a href="<?= Url::to([
        'staff/agenda',
        'day' => $nextDay
    ]) ?>" class="btn btn-default">
        <?= \Yii::t('app', "Next day"); ?>
        <span class="glyphicon glyphicon-chevron-right"></span>
    </a>

</div>


<div id="cal_datepicker" class="row">

    <!--
        Tabs usage reference: http://getbootstrap.com/javascript/#tabs-usage
    -->
    <?php
    $businessHours = [//TODO Make these dynamic
        'start' => '8:00',
        'end' => '19:00'
    ];

    /**
     * We will use this to track the interval of hours to show in the calendar
     * Initially it will be equal to the business hours, but if there are explicit
     * timeslots exceeding it, we should make space for them too
     */
    $borders = $businessHours;

    $events = [
        [//Show Business Hours.
            'start' => $businessHours['start'],
            'end' => $businessHours['end'],
            'dow' => [0, 1, 2, 3, 4, 5, 6],
            'rendering' => 'inverse-background',
            'className' => 'closed'
        ]
    ];
//    array_pop($simulators);
//    array_pop($simulators);
//    array_pop($simulators);
    echo "<div id='calendar' class='col-md-10'>";
    echo "<ul id='simulatorTab' class='nav nav-tabs'>";
    $counter = 0;
    foreach ($simulators as $simulator) {
        $href = "#sim" . $counter++;
        echo "<li role='presentation'><a href='$href' data-toggle='tab'>$simulator->name</a></li>";
    }
    echo "</ul>";
    echo "<div class='tab-content'>";
    $counter = 0;
    foreach ($simulators as $simulator) {
        $id = "sim" . $counter++;
        echo "<div role='tabpanel' class='tab-pane fade' id='$id'>";
        echo FullCalendar::widget([
            'config' => [
                'header' => [
                    'left' => '',
                    'center' => 'title',
                    'right' => ''
                ],
                'aspectRatio' => '2',
                'defaultView' => 'agendaDay',
                'scrollTime' => '08:00:00',
                'editable' => false,
                'firstDay' => 1,
                'allDaySlot' => false,
                'defaultDate' => $currDay->format("c"),
                //'events' => $events,
                //'eventRender' => new \yii\web\JsExpression('slotBooking'),
                //Features for booking during weekdays
                'selectable' => true,
                'selectOverlap' => new \yii\web\JsExpression("function(event)
                {
                    return (event.rendering === 'background' || event.rendering === 'inverse-background');
                }"),
                'selectConstraint' => [
                    'start' => $businessHours['start'],
                    'end' => $businessHours['end'],
                    'dow' => [0, 1, 2, 3, 4, 5, 6]
                ],
                'minTime' => $borders['start'],
                'maxTime' => $borders['end'],
                //'select' => new \yii\web\JsExpression("goToCreateWeekdays")
            ]
        ]);
        echo "</div>";
    }
    echo "</div>";
    echo "</div>";
    ?>
    <div id="datepicker" class="col-md-2">

        <label><?= \Yii::t('app', "Pick a date"); ?></label>
        <?php
        $agenda_url = Url::to([
            'staff/agenda'
        ]);
        echo DatePicker::widget([
            'name' => 'dp_1',
            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
            'pluginOptions' => [
                'todayHighlight' => true,
                'todayBtn' => true,
                'weekStart' => '1',
                'startDate' => 'today',
            ],
            'pluginEvents' => ["changeDate" => "function(e){document.location.href='" . $agenda_url . "?day='+e.date.getDateString();}"],

        ]);
        ?>
    </div>
</div>

<?php
    $this->registerJs("
        $(function () {
            $('#simulatorTab a').click(function (e) {
              e.preventDefault();
              $(this).tab('show');
            });
            $('a[data-toggle=\'tab\']').on('shown.bs.tab', function (e) {
                id = e.currentTarget.href[e.currentTarget.href.length - 1];
                str = '#w' + id;
                $(str).fullCalendar('render');
            });
            $('#simulatorTab a:first').tab('show');
        });


    ");
?>

<script type="text/javascript">
    //Get formatted string
    Date.prototype.getDateString = function () {
        var date = new Date(this.getTime());
        var day = date.getUTCDate() + 1;
        var month = date.getUTCMonth() + 1;
        var year = date.getUTCFullYear();
        return "" + year + "-" + month + "-" + day;
    };
</script>