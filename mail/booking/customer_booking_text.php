<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 06/01/2015
 * Time: 18:18
 */

echo "Here there are your booking information: \n
Your Secret key is ". $booking->token . "\n\n
You have booked the following simulator: \n";
Yii::info($timeslots);
foreach($timeslots as $slot){
    //$simulator = $slot->getSimulator();

    echo ""." \n
    starting from ". $slot->start . "\n
    ending at " . $slot->end. "\n\n";

}
