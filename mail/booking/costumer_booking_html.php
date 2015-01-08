<?php

use yii\helpers\Html;

/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 06/01/2015
 * Time: 18:18
 */
?>
<div class="customer_booking_html">
<?=
    Html::tag('h1', Yii::t('app', 'Here there are your booking information:'), ['class' => 'mail_bold']).
    Html::tag('p', Yii::t('app', 'Your Secret key is {0}', $booking->token ), ['class' => 'mail_bold'])
?>


<?php
    echo( Html::tag('p', Yii::t('app', 'You have booked the following simulator:'), ['class' => 'mail_bold']));

    foreach($timeSlots as $slot){
        $simulator = $slot->simulator;
        echo(Html::tag('p', Yii::t('app', '{0}', $simulator->name), ['class' => 'mail_bold']));
        echo( Html::ul([
            Yii::t('app', 'starting from {0}', $slot->start ),
            Yii::t('app', 'ending at {0}', $slot->end )]
        ));
    }
?>
</div>