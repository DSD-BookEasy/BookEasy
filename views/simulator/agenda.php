<?php

use app\models\Simulator;
use yii\helpers\Html;
use talma\widgets\FullCalendar;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $week string */
/* @var $slots array */
/* @var $simulator integer */
/* @var $currWeek DateTime */
/* @var $prevWeek string */
/* @var $nextWeek string */
/* @var $simulator Simulator */

$price = $simulator->getAttribute("price_simulation");
$duration = $simulator->getAttribute("flight_duration");
$this->title = Yii::t('app', "{simulator}'s agenda", [
    'simulator' => $simulator->getAttribute("name")
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Simulators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $simulator->getAttribute("name")];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Agenda')];
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
            ]) ?>">
                <?= \Yii::t('app', "Previous Week"); ?>
            </a>&nbsp;

            <a href="<?= Url::to([
                'simulator/agenda',
                'id' => $simulator->getAttribute("id"),
                'week' => $nextWeek

            ]) ?>">
                <?= \Yii::t('app', "Next Week"); ?>
            </a>
        </div>

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
    <script type="text/javascript">
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
            }

            $d.html('<?=\Yii::t('app',"Do you want to send a request for making a special booking for this simulator?") ?><br />\
            <?= \Yii::t('app',"Starting from")?>: <span class="new_timeslot">' + start.format("d-M-YYYY HH:mm") + '</span> <br />\
            <?= \Yii::t('app',"Ending")?>: <span class="new_timeslot">' + end.format("d-M-YYYY HH:mm") + '</span>');

            $d.dialog({
                modal: true,
                buttons: {
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
                }
            });
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