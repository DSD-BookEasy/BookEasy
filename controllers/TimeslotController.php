<?php

namespace app\controllers;

use Yii;
use app\models\Timeslot;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * TimeslotController implements the CRUD actions for Timeslot model.
 */
class TimeslotController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Timeslot models.
     * Expects GET parameters:
     * simulator: the id of the simulator for which to show timeslots
     * week: the week for which timeslots should be shown
     * @return mixed
     * @throws ErrorException if simulator parameter is missing
     */
    public function actionIndex()
    {
        $sim = \Yii::$app->request->get("simulator");
        if ($sim == null || ((int)$sim) == 0) {
            throw new ErrorException();
        }
        $show = \Yii::$app->request->get("week", date('c'));
        if (strtotime($show) == false) {
            $show = date('c');
        }

        //Find timeslots in the week of the specified day
        $borders = $this->findWeekFromDay($show);
        $thisWeek = Timeslot::find()->
        where(['id_simulator' => $sim])->
        andWhere(['>=', 'start', strftime("%Y-%m-%d", $borders[0])])->
        andWhere(['<=', 'end', strftime("%Y-%m-%d", $borders[1])])->all();

        return $this->render('index', [
            'week' => $show,
            'slots' => $thisWeek
        ]);
    }

    /**
     * Finds the week borders given a day in that week
     * @param integer|string $day the day to use for finding the week
     * @return array with the monday in position 0 and sunday on position 1
     */
    private function findWeekFromDay($day){
        $secsInDay=60*60*24;
        if(is_string($day)){
            $day=strtotime($day);
        }

        $dayOfWeek=strftime("%u",$day)-1;
        $monday=$day-($secsInDay*$dayOfWeek);
        $sunday=$monday+($secsInDay*6);

        return [$monday,$sunday];
    }
}
