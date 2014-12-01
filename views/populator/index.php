<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */

?>

<div class="alert alert-warning" role="alert">
    <p><b>Warning! </b>
        Make sure you run this controller just once, otherwise it will duplicate your data. If you want to
        regenerate
        the fixture files run the below code on
        your project root folder. But before that you need to create the <code>/tests/codeception/config.php</code> file
        like
        the example in the same path.
    </p>

    <p>
        <code>/tests/codeception/bin/yii fixture/generate (fixturename) --count=(number) --language='sv_SE'</code>
        refer to <a href="http://www.yiiframework.com/doc-2.0/guide-test-fixtures.html" target="_blank">here</a> and
        <a
            target="_blank" href="https://github.com/fzaninotto/Faker">faker</a>
    </p>

    <p>
        You can also edit the files in <code>/tests/codeception/templates/</code>
    </p>

    <?php #echo $debug?>
</div>
<div class="alert alert-info" role="alert">
    <p>
        All staff passwords are <code>123456789</code>.
    </p>

    <p>
        This controller generates only 10 staff members, 4 simulators, 20 bookings(randomly assigned to a staff or
        unassigned, random status(we don't have approval yet)), 22 Timeslots, 0 Timeslotmodels(we don't have it
        yet).
        If you want to generate more data or change a little bit, explore this controller and try generating more
        data with faker and fixtures.
        This controller generates only serial timeslots within the time between 1 December and 7 December(especially
        on Sunday).
        Take a look at the controller code to change these dates.
    </p>
</div>
<div>
    <form style="float:left" method="get" action="<?= \yii\helpers\Url::to(['execute']) ?>">
        <input type="submit" class="btn btn-warning btn-lg" value="Click me once">
    </form>
    <form method="get" action="<?= \yii\helpers\Url::to(['clear']) ?>"
          onsubmit="return confirm('This action will completely remove every row in your database. Are you sure?');">
        <input style="margin-left: 50px" type="submit" class="btn btn-danger btn-lg" value="Clear database">
    </form>
</div>