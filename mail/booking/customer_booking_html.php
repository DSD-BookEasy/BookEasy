<?php

use app\models\Booking;
use app\models\Timeslot;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $booking Booking */
/* @var $totalSimulationCost integer */
/* @var $entryFee integer */
/* @var $timeslots Timeslot[] */

$style_table = 'border-spacing: 0;border-collapse: collapse!important;background-color: transparent;width: 100%;max-width: 100%;margin-bottom: 20px;';
$style_td_even = 'padding: 8px;line-height: 1.42857143;vertical-align: top;border-top: 1px solid #ddd;background-color: #fff!important;';
$style_td_odd = 'padding: 8px;line-height: 1.42857143;vertical-align: top;border-top: 1px solid #ddd;background-color: #fafafa!important;';
$style_btn = 'background-color: #337ab7;color: #fff;text-decoration: none;display: inline-block;padding: 6px 12px;margin-bottom: 0;font-size: 14px;font-weight: 400;line-height: 1.42857143;text-align: center;white-space: nowrap;vertical-align: middle;-ms-touch-action: manipulation;touch-action: manipulation;cursor: pointer;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;background-image: none;border: 1px solid transparent;border-radius: 4px;border-color: #2e6da4;';

?>

<?= Html::tag('p',
    Yii::t('app', 'Hello, {0} <br> we have received your request. Below you can find a summary of your booking:',
        $booking->name), ['style' => 'margin: 0 0 10px;']) ?>

<table class="table table-striped" style="<?= $style_table ?>">
    <tbody>
    <tr>
        <?= Html::tag('td', Yii::t('app', 'Name'), ['style' => $style_td_odd]) ?>
        <?= Html::tag('td', $booking->name . ' ' . $booking->surname, ['style' => $style_td_odd]) ?>
    </tr>
    <tr>
        <?= Html::tag('td', Yii::t('app', 'Status'), ['style' => $style_td_even]) ?>
        <?= Html::tag('td', $booking->status, ['style' => $style_td_even]) ?>
    </tr>
    <tr>
        <?= Html::tag('td', Yii::t('app', 'Secret key'), ['style' => $style_td_odd]) ?>
        <?= Html::tag('td', $booking->token, ['style' => $style_td_odd]) ?>
    </tr>
    <tr>
        <?= Html::tag('td', Yii::t('app', 'Flight cost'), ['style' => $style_td_even]) ?>
        <?= Html::tag('td', $totalSimulationCost .' kr', ['style' => $style_td_even]) ?>
    </tr>
    <tr>
        <?= Html::tag('td', Yii::t('app', 'Entry fee'), ['style' => $style_td_odd]) ?>
        <?= Html::tag('td', $entryFee .' kr', ['style' => $style_td_odd]) ?>
    </tr>
    <tr>
        <?= Html::tag('td', Yii::t('app', 'Total cost'), ['style' => $style_td_even]) ?>
        <?= Html::tag('td', $totalSimulationCost + $entryFee .' kr', ['style' => $style_td_even]) ?>
    </tr>
    </tbody>
</table>

<h3 style="page-break-after: avoid;font-family: inherit;font-weight: 500;line-height: 1.1;color: inherit;margin-top: 20px;margin-bottom: 10px;font-size: 24px;"><?= Yii::t('app', 'Booked simulations') ?></h3>

<?php

foreach ($timeslots as $timeslot) {
    $simulator = $timeslot->simulator;
    ?>
    <table class="table table-striped" style="<?= $style_table ?>">
        <tbody>
        <tr>
            <?= Html::tag('td', Yii::t('app', 'Simulator'), ['style' => $style_td_odd]) ?>
            <?= Html::tag('td', $simulator->name, ['style' => $style_td_odd]) ?>
        </tr>
        <tr>
            <?= Html::tag('td', Yii::t('app', 'Start'), ['style' => $style_td_even]) ?>
            <?= Html::tag('td', $timeslot->start, ['style' => $style_td_even]) ?>
        </tr>
        <tr>
            <?= Html::tag('td', Yii::t('app', 'End'), ['style' => $style_td_odd]) ?>
            <?= Html::tag('td', $timeslot->end, ['style' => $style_td_odd]) ?>
        </tr>
        <tr>
            <?= Html::tag('td', Yii::t('app', 'Cost'), ['style' => $style_td_even]) ?>
            <?= Html::tag('td', $timeslot->calculateCost() .' kr', ['style' => $style_td_even]) ?>
        </tr>
        </tbody>
    </table>

    <hr style="height: 0;margin-top: 20px;margin-bottom: 20px;border: 0;border-top: 1px solid #eee;">
<?php
}
?>
<p style="margin: 0 0 10px;">
    <a class="btn btn-primary" style="<?= $style_btn ?>" href="<?= Url::to(['view', 'id' => $booking->id, 'token' => $booking->token], true) ?>"><?= Yii::t('app', 'View or cancel') ?></a><?= Yii::t('app', ' your booking on our website') ?>
</p>
