<?php

use app\models\Simulator;
use kartik\date\DatePicker;
use yii\helpers\Html;
use talma\widgets\FullCalendar;
use yii\helpers\Url;


/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $week string */
/* @var $slots array */
/* @var $simulator integer */
/* @var $currWeek DateTime */
/* @var $prevWeek string */
/* @var $nextWeek string */
/* @var $simulator Simulator */
/* @var $model app\models\DatePicker */

$price = $simulator->getAttribute("price_simulation");
$duration = $simulator->getAttribute("flight_duration");
$this->title = Yii::t('app', "{simulator}'s agenda", [
    'simulator' => $simulator->getAttribute("name")
]);

?>


    <div class="simulator-availability">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= \Yii::t('app', 'Click on a timeslot to make a booking'); ?></p>
    <p><?= \Yii::t('app', 'Click on an empty spot in the calendar to send a request for a special booking'); ?></p>

    <div id="calendar_buttons">
        <a href="<?= Url::to([
                'simulator/agenda',
                'id' => $simulator->getAttribute("id"),
                'week' => $prevWeek
            ]) ?>"  class="btn btn-default glyphicon glyphicon-chevron-left">
                <?= \Yii::t('app', "Previous week"); ?>
            </a>

        <a href="<?= Url::to([
                'simulator/agenda',
                'id' => $simulator->getAttribute("id"),
            ]) ?>" class="btn btn-default">
                <?= \Yii::t('app', "Today"); ?>
            </a>

        <a href="<?= Url::to([
                'simulator/agenda',
                'id' => $simulator->getAttribute("id"),
                'week' => $nextWeek
            ]) ?>" class="btn btn-default glyphicon glyphicon-chevron-right">
                <?= \Yii::t('app', "Next week"); ?>
            </a>

    </div>

    <div id="cal_datepicker" class="row">
        <div id="calendar" class="col-md-10">
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
            //Populate calendar events with timeslots
            foreach ($slots as $s) {
                $a = [
                    'start' => $s->start,
                    'end' => $s->end,
                    'id' => $s->id
                ];
                if ($s->id_booking != null) {
                    $a['title'] = \Yii::t('app', 'Unavailable');
                    $a['className'] = 'unavailable';
                } else {
                    $a['title'] = \Yii::t('app', 'Available');
                    $a['className'] = 'available';
                }
                checkBorders($borders, $s->start, $s->end);
                $events[] = $a;
            }

            $bookUrl = Url::to(['booking/create', 'timeslots[]' => '']);
            $bookUrlWeekday = Url::to(['booking/create-weekdays']);

            echo FullCalendar::widget([
                'config' => [
                    'header' => [
                        'left' => '',
                        'center' => 'title',
                        'right' => ''
                    ],
                    'aspectRatio' => '2',
                    'defaultView' => 'agendaWeek',
                    'scrollTime' => '08:00:00',
                    'editable' => false,
                    'firstDay' => 1,
                    'allDaySlot' => false,
                    'defaultDate' => $currWeek->format("c"),
                    'events' => $events,
                    'eventRender' => new \yii\web\JsExpression('slotBooking'),
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
                    'select' => new \yii\web\JsExpression("goToCreateWeekdays")
                ]
            ]); ?>
        </div>
        <div id="datepicker" class="col-md-2">

            <label><?= \Yii::t('app', "Pick a date"); ?></label>
            <?php
            $agenda_url = Url::to([
                'simulator/agenda',
                'id' => $simulator->getAttribute("id"),
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
                'pluginEvents' => ["changeDate" => "function(e){document.location.href='" . $agenda_url . "?week='+e.date.getWeekYear()+'W'+e.date.getWeek();}"],

            ]);
            ?>
        </div>

    </div>
</div>
<script type="text/javascript">
    Date.prototype.getWeek = function() {
        var date = new Date(this.getTime());
        date.setHours(0, 0, 0, 0);
        // Thursday in current week decides the year.
        date.setDate(date.getDate() + 3 - (date.getDay() + 6) % 7);
        // January 4 is always in week 1.
        var week1 = new Date(date.getFullYear(), 0, 4);
        // Adjust to Thursday in week 1 and count number of weeks from date to week1.
        var $result = 1 + Math.round(((date.getTime() - week1.getTime()) / 86400000
        - 3 + (week1.getDay() + 6) % 7) / 7);

        if ( $result < 10 ) {
            $result = '0' + $result;
        }

        return $result;
    };

    // Returns the four-digit year corresponding to the ISO week of the date.
    Date.prototype.getWeekYear = function() {
        var date = new Date(this.getTime());
        date.setDate(date.getDate() + 3 - (date.getDay() + 6) % 7);
        return date.getFullYear();
    };

    function slotBooking(event, element) {
        if (event.rendering != "background" && event.rendering != "inverse-background") {
            if (element.hasClass("available")) {
                element.attr("title", "<?=\Yii::t('app',"This timeslot is available. and it costs {price}SEK for {duration} minutes",[
                'price' => $price,
                'duration' => $duration
                ])?>");

                element.tooltip();
                element.click(function (ev) {
                    ev.preventDefault();
                    window.location.href = "<?=$bookUrl?>" + event.id;
                })
            }
            else {
                element.attr("title", "<?=\Yii::t('app',"This timeslot is already booked. Choose another one, please.")?>");
                element.tooltip();
            }
        }
    }

    function goToCreateWeekdays(start, end, jsEvent) {
        var $d = $('#dialog');
        if ($d.length == 0) {
            $('body').append('<div id="dialog"></div>');
            $d = $('#dialog');
            $d.dialog({
                modal: true
            });
        }

        $d.html('<?=\Yii::t('app',"Do you want to send a request for making a special booking for this simulator?") ?><br />\
        <?= \Yii::t('app',"Starting from")?>: <span class="new_timeslot">' + start.format("d-M-YYYY HH:mm") + '</span> <br />\
        <?= \Yii::t('app',"Ending")?>: <span class="new_timeslot">' + end.format("d-M-YYYY HH:mm") + '</span>');
        $d.dialog("option", "buttons", [{
            "<?=\Yii::t('app',"Confirm");?>": function () {
                window.location.href = '<?=$bookUrlWeekday?>?'
                + encodeURIComponent('timeslots[0][start]') + '=' + encodeURIComponent(start.format())
                + '&' + encodeURIComponent('timeslots[0][end]') + '=' + encodeURIComponent(end.format())
                + '&' + encodeURIComponent('timeslots[0][id_simulator]') + '=' + getSimulatorId();
            },
            "<?= \Yii::t('app',"Cancel")?>": function () {
                $('.fullcalendar').fullCalendar('unselect');
                $(this).dialog("close");
            }
        }]);
    }

    function getSimulatorId() {
        var url = window.location.href;
        var paths = url.split('/');
        return paths[paths.length - 2];
    }
</script>
<?php
function checkBorders(&$borders, $start, $end)
{
    //Converting hours in minutes
    $convertStart = ((int)strftime("%H", strtotime($start))) * 60 + (int)strftime("%M", strtotime($start));
    $convertEnd = ((int)strftime("%H", strtotime($end))) * 60 + (int)strftime("%M", strtotime($end));
    $chunks = explode(':', $borders['start']);
    $convertBStart = $chunks[0] * 60 + $chunks[1];
    $chunks = explode(':', $borders['end']);
    $convertBEnd = $chunks[0] * 60 + $chunks[1];
    if ($convertBStart > $convertStart) {
        $borders['start'] = strftime("%H:%M", strtotime($start));
    }
    if ($convertBEnd < $convertEnd) {
        $borders['end'] = strftime("%H:%M", strtotime($end));
    }
}