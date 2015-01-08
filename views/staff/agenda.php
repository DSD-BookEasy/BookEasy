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
    <div id="cal_datepicker" style="position: relative; padding-top: 120px">

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
                'className' => 'closedSlot'
            ]
        ];
        echo "<div id='calendar' class='col-md-12'>";
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
                    'className' => 'closedSlot'
                ]
            ];
            $nowDate = new DateTime('now');
            //Populate calendar events with timeslots
            foreach ($slots[$simulator->id] as $slot) {
                $event = [
                    'start' => $slot->start,
                    'end' => $slot->end,
                    'id' => $slot->id
                ];
                if ($slot->id_booking != null) {
                    $event['id_booking'] = $slot->id_booking; //to be able to view booking page we need to store the id
                    if ($bookings[$slot->id_booking]->assigned_instructor != null) {
                        //if the booking is assigned that show the name
                        $iid = $bookings[$slot->id_booking]->assigned_instructor;
                        $event['title'] = \Yii::t('app',
                            '{name} {lname} has booked this slot, which is assigned to {ins_name} {ins_lname}, on {ts} ',
                            [
                                'name' => $bookings[$slot->id_booking]->name,
                                'lname' => $bookings[$slot->id_booking]->surname,
                                'ts' => $bookings[$slot->id_booking]->timestamp,
                                'ins_name' => $staff[$iid]->name,
                                'ins_lname' => $staff[$iid]->surname,
                            ]);
                        $event['className'] = 'assigned';
                    } else {
                        //the booking is not assigned then warn the user about it
                        $event['title'] = \Yii::t('app',
                            '{name} {lname} has booked this slot, which is NOT assigned, on {ts} ', [
                                'name' => $bookings[$slot->id_booking]->name,
                                'lname' => $bookings[$slot->id_booking]->surname,
                                'ts' => $bookings[$slot->id_booking]->timestamp,
                            ]);
                        $event['className'] = 'unassigned';
                    }
                } else {
                    $event['title'] = \Yii::t('app', 'Available');
                    $event['className'] = 'available';
                }

                if ($slot->blocking) {
                    $event['title'] = \Yii::t('app', 'Closed');
                    $event['className'] = 'closedSlot closed_dayview';
                }
                $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $slot->start);
                if ($startDate < $nowDate) {
                    $event['className'] = "passedSlot";
                }
                checkBorders($borders, $slot->start, $slot->end);
                $events[] = $event;
            }
            echo FullCalendar::widget([
                'config' => [
                    'header' => [
                        'left' => '',
                        'center' => 'title',
                        'right' => ''
                    ],
                    'aspectRatio' => '2.4',
                    'defaultView' => 'agendaDay',
                    'scrollTime' => '08:00:00',
                    'editable' => false,
                    'firstDay' => 1,
                    'allDaySlot' => false,
                    'defaultDate' => $currDay->format("c"),
                    'events' => $events,
                    'eventRender' => new \yii\web\JsExpression('slotSelecting'),
                    'minTime' => $borders['start'],
                    'maxTime' => $borders['end'],
                ]
            ]);
            echo "</div>"; //end of the bootstrap div for each tab
        }
        echo "</div>"; // end of the bootstrap div which contains all the tabs
        echo "</div>"; // end of the <div id='calendar' ....>  which contains all the calendars
        ?>
        <div id="calendar_tools">
            <div id="calendar_buttons">
                <a id="prevButton" href="<?= Url::to([
                    '/staff/agenda',
                    'day' => $prevDay
                ]) ?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </a>

                <a id="todayButton" href="<?= Url::to([
                    '/staff/agenda',
                ]) ?>" class="btn btn-default">
                    <?= \Yii::t('app', "Today"); ?>
                </a>

                <a id="nextButton" href="<?= Url::to([
                    '/staff/agenda',
                    'day' => $nextDay
                ]) ?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </a>

            </div>
            <?php
            $agenda_url = Url::to([
                '/staff/agenda'
            ]);
            echo DatePicker::widget([
                'name' => 'dp_1',
                'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                'options' => ['placeholder' => \Yii::t('app', "Pick a date ") . 'mm/dd/yyyy'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'weekStart' => '1',
                ],
                'pluginEvents' => ["changeDate" => "function(e){document.location.href='" . $agenda_url . "?day='+e.date.getDateString();}"],

            ]);
            ?>
        </div>
    </div>
    <ul id="colorMap" style="list-style: none; text-align: center">
        <li class="available"><span><?= \Yii::t('app', 'Available'); ?></span></li>
        <li class="assigned"><span><?= \Yii::t('app', 'Booked and assigned'); ?></span></li>
        <li class="unassigned"><span><?= \Yii::t('app', 'Booked and unassigned'); ?></span></li>
        <li class="passedSlot"><span><?= \Yii::t('app', 'Past slot'); ?></span></li>
        <li class="closedSlot"><span><?= \Yii::t('app', 'Closed hours'); ?></span></li>
    </ul>
<?php
$this->registerJs("
        var selectedSim = 'sim0';
        function setHref(element, id) {
            val = element.attr('href');
            indexSharp = val.indexOf('#');
            if (indexSharp > 0) {
                val = val.substring(0,indexSharp);
            }
            element.attr('href',val + id);
        }
        $(function () {
            $('#simulatorTab a').click(function (e) {
                e.preventDefault();
                selectedSim = $(this).attr('href');
                setHref($('#prevButton'), selectedSim);
                setHref($('#todayButton'), selectedSim);
                setHref($('#nextButton'), selectedSim);
                $(this).tab('show');
            });
            $('a[data-toggle=\'tab\']').on('shown.bs.tab', function (e) {
                id = e.currentTarget.href[e.currentTarget.href.length - 1];
                str = '#w' + id;
                $(str).fullCalendar('render');
            });
            indexSharp = window.location.href.indexOf('#');
            sim_id = '';
            if (indexSharp > -1) {
                sim_id = window.location.href.substring(indexSharp);
            }
            if (sim_id.length > 0) {
                setHref($('#prevButton'), sim_id);
                setHref($('#todayButton'), sim_id);
                setHref($('#nextButton'), sim_id);
                $('#simulatorTab a[href=' + sim_id + ']').tab('show');
            }
            else {
                $('#simulatorTab a:first').tab('show');
            }
        });");
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
                if (element.hasClass("passedSlot")) {
                    element.attr("title", "<?=\Yii::t('app',"This timeslot is expired")?>");
                    element.tooltip();
                }
                else if (element.hasClass("available")) {
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
                } else if (element.hasClass("closedSlot")) {
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