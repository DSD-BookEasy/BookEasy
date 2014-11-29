<?php

namespace app\controllers;

use Yii;
use app\models\Timeslot;
use app\models\TimeslotSearch;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
        $sim=\Yii::$app->request->get("simulator");
        if($sim==null || ((int)$sim)==0){
            throw new ErrorException();
        }
        $show=\Yii::$app->request->get("week",date('c'));
        if(($show=strtotime($show))==false){
            $show=time();
        }

        //Find timeslots in the week of the specified day
        $borders=$this->findWeekFromDay($show);
        $thisWeek=Timeslot::find()->
            where(['id_simulator' => $sim])->
            andWhere(['>=','start',strftime("%Y-%m-%d",$borders[0])])->
            andWhere(['<=','end',strftime("%Y-%m-%d",$borders[1])])->all();

        return $this->render('index', [
            'week'=>$show,
            'slots'=>$thisWeek
        ]);
    }

    /**
     * Displays a single Timeslot model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Timeslot model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Timeslot();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Timeslot model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Timeslot model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Timeslot model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Timeslot the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Timeslot::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
