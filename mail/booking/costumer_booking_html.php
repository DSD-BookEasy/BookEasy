<?php

use yii\helpers\Html;

/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 06/01/2015
 * Time: 18:18
 */
?>

<?=
    Html::tag('p', Yii::t('app', 'Here there are your booking information:')).
    Html::tag('p', Yii::t('app', 'Your Secret key is {0}', $booking->token ))
?>


<?php
    echo( Html::tag('p', Yii::t('app', 'You have booked the following simulator:')));

    foreach($timeSlots as $slot){
        echo( Html::tag('p', Yii::t('app', 'starting from {0}', $slot->start )));
        echo( Html::tag('p', Yii::t('app', 'ending at {0}', $slot->end )));
    }
?>
