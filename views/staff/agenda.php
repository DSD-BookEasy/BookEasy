<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use talma\widgets\FullCalendar;
use yii\helpers\Url;
use app\models\Parameter;


/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\DatePicker */
/* @var $simulators array */
/* @var $slots array */
/* @var $bookings array */
/* @var $staff array */
/* @var $currDay DateTime */
/* @var $prevDay string */
/* @var $nextDay string */

$this->title = Yii::t('app', "Staff Agenda");
?>

    <div id="calendar_buttons">
        <a href="<?= Url::to([
            '/staff/agenda',
            'day' => $prevDay
        ]) ?>" class="btn btn-default">
            <span class="glyphicon glyphicon-chevron-left"></span>
            <?= \Yii::t('app', "Previous day"); ?>
        </a>

        <a href="<?= Url::to([
            '/staff/agenda',
        ]) ?>" class="btn btn-default">
            <?= \Yii::t('app', "Today"); ?>
        </a>

        <a href="<?= Url::to([
            '/staff/agenda',
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
        $businessHours = [
            'start' => Parameter::getValue('businessTimeStart'),
            'end' => Parameter::getValue('businessTimeEnd')
        ];

        /**
         * We will use this to track the interval of hours to show in the calendar
         * Initially it will be equal to the business hours, but if there are explicit
         * timeslots exceeding it, we should make space for them too
         */
        $borders = $businessHours;
        $bookUrl = Url::to(['/booking/create', 'timeslots[]' => '']);
        $bookUrlView = Url::to(['/booking']);
        $bookUrlWeekday = Url::to(['/booking/create-weekdays']);
        $timeSlotUrl = Url::to(['/timeslot']);
        $events = [
            [//Show Business Hours.
                'start' => $businessHours['start'],
                'end' => $businessHours['end'],
                'dow' => [0, 1, 2, 3, 4, 5, 6],
                'rendering' => 'inverse-background',
                'className' => 'closed'
            ]
        ];
        echo "<div id='calendar' class='col-md-10'>";
        echo "<ul id='simulatorTab' class='nav nav-tabs'>";
        $counter = 0;
        foreach ($simulators as $simulator) {
            $href = "#sim" . $counter++; //the id of the targeting tab
            echo "<li role='presentation'><a href='$href' data-toggle='tab'>$simulator->name</a></li>";
        }
        echo "</ul>";//ending tag of the <ul id='simulatorTab' ...>
        echo "<div class='tab-content'>"; //the bootstrap div that contains all the tabs
        $counter = 0;
        foreach ($simulators as $simulator) {
            //foreach simulator generate a tab
            $id = "sim" . $counter++; //the id of the tab which is going to be created
            echo "<div role='tabpanel' class='tab-pane fade' id='$id'>"; //bootstrap div that contains a tab
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
            foreach ($slots[$simulator->id] as $s) {
                $a = [
                    'start' => $s->start,
                    'end' => $s->end,
                    'id' => $s->id
                ];
                if ($s->id_booking != null) {
                    $a['id_booking'] = $s->id_booking; //to be able to view booking page we need to store the id
                    if ($bookings[$s->id_booking]->assigned_instructor != null) {
                        //if the booking is assigned that show the name
                        $iid = $bookings[$s->id_booking]->assigned_instructor;
                        $a['title'] = \Yii::t('app', '{name} {lname} has booked this slot, which is assigned to {ins_name} {ins_lname}, on {ts} ', [
                            'name' => $bookings[$s->id_booking]->name,
                            'lname' => $bookings[$s->id_booking]->surname,
                            'ts' => $bookings[$s->id_booking]->timestamp,
                            'ins_name' => $staff[$iid]->name,
                            'ins_lname' => $staff[$iid]->surname,
                        ]);
                        $a['className'] = 'assigned';
                    } else {
                        //the booking is not assigned then warn the user about it
                        $a['title'] = \Yii::t('app', '{name} {lname} has booked this slot, which is NOT assigned, on {ts} ', [
                            'name' => $bookings[$s->id_booking]->name,
                            'lname' => $bookings[$s->id_booking]->surname,
                            'ts' => $bookings[$s->id_booking]->timestamp,
                        ]);
                        $a['className'] = 'unassigned';
                    }
                } else {
                    $a['title'] = \Yii::t('app', 'Available');
                    $a['className'] = 'available';
                }

                if ($s->blocking) {
                    $a['title'] = \Yii::t('app', 'Closed');
                    $a['className'] = 'closed closed_dayview';
                }
                checkBorders($borders, $s->start, $s->end);
                $events[] = $a;
            }
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
                    'events' => $events,
                    'eventRender' => new \yii\web\JsExpression('slotSelecting'),
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
            echo "</div>"; //end of the bootstrap div for each tab
        }
        echo "</div>"; // end of the bootstrap div which contains all the tabs
        echo "</div>"; // end of the <div id='calendar' ....>  which contains all the calendars
        ?>
        <div id="datepicker" class="col-md-2">

            <label><?= \Yii::t('app', "Pick a date"); ?></label>
            <?php
            $agenda_url = Url::to([
                '/staff/agenda'
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

        function slotSelecting(event, element) {
            if (event.rendering != "background" && event.rendering != "inverse-background") {
                if (element.hasClass("available")) {
                    element.attr("title", "<?=\Yii::t('app',"This timeslot is available click the slot to create a booking")?>");
                    element.tooltip();
                    element.click(function (ev) {
                        ev.preventDefault();
                        window.location.href = "<?=$bookUrl?>" + event.id;
                    });
                }
                else if (element.hasClass("assigned")) {
                    element.attr("title", "<?=\Yii::t('app',"Click the timeslot to edit the booking")?>");
                    element.tooltip();
                    element.click(function (ev) {
                        ev.preventDefault();
                        window.location.href = "<?=$bookUrlView?>" + "/" + event.id_booking + "/view";
                    });
                }
                else if (element.hasClass("unassigned")) {
                    element.attr("title", "<?=\Yii::t('app',"Click the timeslot to edit the booking")?>");
                    element.tooltip();
                    element.click(function (ev) {
                        ev.preventDefault();
                        window.location.href = "<?=$bookUrlView?>" + "/" + event.id_booking + "/view";
                    });
                } else if (element.hasClass("closed")){
                    element.attr("title", "<?=\Yii::t('app',"Click the timeslot to edit the booking")?>");
                    element.tooltip();
                    element.click(function (ev) {
                        ev.preventDefault();
                        window.location.href = "<?=$timeSlotUrl?>" + "/" + event.id + "/view";
                    });
                }
            }
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