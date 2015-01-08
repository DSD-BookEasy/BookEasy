<?php

use app\models\Simulator;
use kartik\date\DatePicker;
use yii\helpers\Html;
use talma\widgets\FullCalendar;
use yii\helpers\Url;
use app\models\Parameter;


/* @var $form yii\bootstrap\ActiveForm */
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $week DateTime */
/* @var $slots array */
/* @var $simulator integer */
/* @var $currWeek string */
/* @var $prevWeek string */
/* @var $nextWeek string */
/* @var $simulator Simulator */
/* @var $simulators app\models\Simulator[]*/

$price = $simulator->price_simulation;
$duration = $simulator->flight_duration;
?>
    <div class="simulator-availability">
        <h1>
            <?= Html::encode(Yii::t('app', "{simulator}", ['simulator' => $simulator->name])) ?>
        </h1>

        <p class="lead">
            <?= $simulator->description ?>
        </p>

        <p>
            <?= Html::ul([
                Yii::t('app', 'Click on an available time slot to book this simulator inside the regular opening hours of the museum.'),
                Yii::t('app', 'Click on an empty spot in the calendar to make a request for a booking outside the opening hours or hold and drag to request a longer time span.'),
                Yii::t('app', 'Keep in mind, that bookings outside of the regular opening hours have to be confirmed by the museum.'),
            ]) ?>
        </p>

        <div class="row text-center">
            <!-- Shows a selection of all available simulators. -->
            <?php foreach ($simulators as $simulator_model) { ?>
                <!-- The simulators will be aligned in a column with width 2 of 12. Alignment seems to work fine this way. -->
                <div class="align-simulator"> <!--class="col-md-2"-->
                    <!-- Create a click able picture linked to the corresponding simulators agenda. -->
                    <p>
                        <a href="<?= Url::to(['/simulator/agenda', 'id' => $simulator_model->id, 'week' => $currWeek]);?>">
                            <?php
                            if ($simulator_model->getImage()) {
                                echo Html::img('@web/' . $simulator_model->getImage()->getPath('125x'),
                                    ['alt' => Yii::t('app', 'Simulator image')]);
                            } else {
                                echo Html::img('http://placehold.it/125',
                                    ['alt' => Yii::t('app', 'Simulator image')]);
                            }
                            ?>
                        </a>
                    </p>

                    <!-- Display the simulators name -->
                    <h4>
                        <?= $simulator_model->getAttribute("name") ?>
                    </h4>
                </div>
            <?php } ?>
        </div>


        <div style="position: relative">
            <div id="calendar" style="padding-top: 110px;">
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

                    $events = [
                        [//Show Business Hours.
                            'start' => $businessHours['start'],
                            'end' => $businessHours['end'],
                            'dow' => [0, 1, 2, 3, 4, 5, 6],
                            'rendering' => 'inverse-background',
                            'className' => 'closed'
                        ]
                    ];
                    $nowDate = new DateTime('now');
                    //Populate calendar events with timeslots
                    foreach ($slots as $slot) {
                        $event = [
                            'start' => $slot->start,
                            'end' => $slot->end,
                            'id' => $slot->id
                        ];
                        if ($slot->id_booking != null) {
                            $event['title'] = \Yii::t('app', 'Unavailable');
                            $event['className'] = 'unavailable';
                        } else {
                            $event['title'] = \Yii::t('app', 'Available');
                            $event['className'] = 'available';
                        }

                        if($slot->blocking) {
                            $event['title'] = \Yii::t('app', 'Closed');
                            $event['className'] = 'closed';
                        }

                        $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $slot->start);
                        if ($startDate < $nowDate) {
                            $event['className'] = "passedSlot";
                        }
                        checkBorders($borders, $slot->start, $slot->end);
                        $events[] = $event;
                    }

                    $bookUrl = Url::to(['/booking/create', 'timeslots[]' => '']);

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
                            'defaultDate' => $week->format("c"),
                            'events' => $events,
                            'eventRender' => new \yii\web\JsExpression('slotBooking'),
                            'minTime' => $borders['start'],
                            'maxTime' => $borders['end']
                        ]
                    ]); ?>
                </div>
            <div style="position: absolute; left:0px; top:0px; display: inline-block" id="calendar_buttons">
            <div style="float: left; width: 20%">
            <?php
            echo Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-chevron-left']),
                ['agenda', 'id' => $simulator->id, 'week' => $prevWeek],
                ['class' => 'btn btn-default']);

            echo Html::a(
                Yii::t('app', "This Week"),
                ['agenda', 'id' => $simulator->id],
                ['class' => 'btn btn-default']);

            echo Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-chevron-right']),
                ['agenda', 'id' => $simulator->id, 'week' => $nextWeek],
                ['class' => 'btn btn-default']);
            ?>
                </div>
            <div style="float: right; width: 80%; display: inline-block">

            <label style="float: left; margin-right: 25px; margin-top: 7px"><?= \Yii::t('app', "Pick a date"); ?></label>
            <?php
            $agenda_url = Url::to([
                'agenda',
                'id' => $simulator->id,
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
        <div class="alert alert-info">
            <?= Html::tag('p',Yii::t('app',"{click}",[
                'click' => Html::a(Yii::t('app','An alternative way to create a booking is by clicking here. (Recommended for mobile devices)'),['booking/create-weekdays','simulator' => $simulator->id])
            ]))?>
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
                if (element.hasClass("passedSlot")) {
                    element.attr("title", "<?=\Yii::t('app',"This timeslot is expired")?>");
                    element.tooltip();
                }
                else if (element.hasClass("available")) {
                    element.attr("title", "<?=\Yii::t('app',"This timeslot is available. and it costs {price} kr for {duration} minutes",[
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
                    element.attr("title", "<?=\Yii::t('app',"This timeslot can't be booked. Choose another one, please.")?>");
                    element.tooltip();
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