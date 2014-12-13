<?php

namespace tests\codeception\unit\models;

use yii\codeception\TestCase;
use app\models\Timeslot;

class TimeslotTest extends TestCase
{
    use \Codeception\Specify;

    private $t1;

    public function testOverlap()
    {

        $this->t1=new Timeslot();
        $this->t1->start='2014-11-12 08:00:00';
        $this->t1->end='2014-11-12 10:00:00';

        $this->specify("timeslot overlapping from before",function(){
            $t2=new Timeslot();
            $t2->start='2014-11-12 07:00:00';
            $t2->end='2014-11-12 09:00:00';
            $this->assertTrue($this->t1->overlapping($t2));
        });

        $this->specify("timeslot overlapping after",function(){
            $t2=new Timeslot();
            $t2->start='2014-11-12 09:00:00';
            $t2->end='2014-11-12 11:00:00';
            $this->assertTrue($this->t1->overlapping($t2));
        });

        $this->specify("timeslot overlaps inside",function(){
            $t2=new Timeslot();
            $t2->start='2014-11-12 08:30:00';
            $t2->end='2014-11-12 09:30:00';
            $this->assertTrue($this->t1->overlapping($t2));
        });

        $this->specify("timeslot overlaps outside",function(){
            $t2=new Timeslot();
            $t2->start='2014-11-12 07:30:00';
            $t2->end='2014-11-12 11:00:00';
            $this->assertTrue($this->t1->overlapping($t2));
        });

        $this->specify("timeslot overlaps exactly",function(){
            $t2=new Timeslot();
            $t2->start='2014-11-12 07:00:00';
            $t2->end='2014-11-12 09:00:00';
            $this->assertTrue($this->t1->overlapping($t2));
        });

        $this->specify("timeslot not overlapping",function(){
            $t2=new Timeslot();
            $t2->start='2014-11-12 12:00:00';
            $t2->end='2014-11-12 13:00:00';
            $this->assertFalse($this->t1->overlapping($t2));
        });
    }
}
