<?php

namespace app\controllers;

use app\models\Timeslot;
use DateTime;
use Yii;
use app\models\Simulator;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * SimulatorController implements the CRUD actions for Simulator model.
 */
class SimulatorController extends Controller
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
     * Lists all simulator models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Simulator::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }



    /**
     * Displays a single simulator model.
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
     * Creates a new simulator model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Simulator();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing simulator model.
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
     * Deletes an existing simulator model.
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
     * Shows available Timeslots for the simulator
     * @params integer $_GET['id']: id of the simulator
     * @params string $_GET['week']: (optional) a valid date representation of the week (preferred: ISO 8601, e.g. 2014W47)
     * @throws NotFoundHttpException: if $_GET['id'] is not set
     * @return mixed
     */
    public function actionAgenda()
    {
        $model= new Simulator;

        $simId = \Yii::$app->request->get("id");

        if (empty($simId) || ((int)$simId) == 0) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $week = \Yii::$app->request->get("week");

        if (empty($week) || !strtotime($week)) {
            // If the week is not set (properly), it's the today's one
            $week = date("Y\WW");
        }

        // Initialize the week
        //if i change the date through the datepicker
        //TODO avoid using POST
        $currWeek = new DateTime($week);
        // and the week before the current one
        $prevWeek = clone $currWeek;
        $prevWeek->modify("previous week");

        // and the week after the current one
        $nextWeek = clone $currWeek;
        $nextWeek->modify("next week + 6 days"); //getting the last day to fix last week of the year problem (53rd week)

        $weekBorders = $this->findWeekBorders($currWeek);

        // Find timeslots in the current week
        $slots = Timeslot::find()->
        where(['id_simulator' => $simId])->
        andWhere(['>=', 'start', $weekBorders['first']->format("c")])->
        andWhere(['<=', 'end', $weekBorders['last']->format("c")])->all();

        // Find simulators
        $simulators = new ActiveDataProvider([
            'query' => \app\models\Simulator::find(),
        ]);
       //
        return $this->render('agenda', [
            'currWeek' => $currWeek,
            'prevWeek' => $prevWeek->format("Y\WW"),
            'nextWeek' => $nextWeek->format("Y\WW"),
            'slots' => $slots,
            'simulator' => $this->findModel($simId),
            'simulators' => $simulators->getModels(),
        ]);


    }

    /**
     * Finds the week's border days given a date in that week
     * @param DateTime $date the day to use for finding the week
     * @return DateTime[] array with the first and last day of the week
     */
    private function findWeekBorders(DateTime $date)
    {

        $borders['first'] = clone $date;
        $borders['first']->modify("this week midnight");

        $borders['last'] = clone $borders['first'];
        $borders['last']->modify("this week + 6 days + 23 hours + 59 minutes + 59 seconds");

        return $borders;
    }

    /**
     * Finds the simulator model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Simulator the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Simulator::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
