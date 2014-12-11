<?php

namespace app\controllers;

use app\models\Simulator;
use Yii;
use app\models\TimeSlotModel;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TimeSlotModelController implements the CRUD actions for TimeSlotModel model.
 */
class TimeSlotModelController extends Controller
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

    public function actionGenerate(){
        //set the value of interval for the creation of timeslot
        $scope = new \DateInterval('P2M');

        TimeSlotModel::generateNextTimeSlot( date_add(new \DateTime(), $scope ));

        return $this->redirect('index', []);
    }

    /**
     * Lists all TimeSlotModel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TimeSlotModel::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TimeSlotModel model.
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
     * Creates a new TimeSlotModel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TimeSlotModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->advanceModelGeneration( new \DateTime($model->start_validity . '+ 3 months') );
            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            $weekDays = [];

            for ($i = 0; $i <= 6; $i++) {
                $weekDays[$i+1] = date('l', strtotime("this week + $i days"));
            }

            $simulators = new ActiveDataProvider([
                'query' => Simulator::find(),
            ]);

            return $this->render('create', [
                'model' => $model,
                'weekDays' => $weekDays,
                'simulators' => $simulators->getModels()
            ]);
        }

    }

    /**
     * Updates an existing TimeSlotModel model.
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
     * Deletes an existing TimeSlotModel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteModel();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TimeSlotModel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TimeSlotModel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TimeSlotModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
