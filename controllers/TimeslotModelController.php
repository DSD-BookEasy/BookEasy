<?php

namespace app\controllers;

use app\models\Simulator;
use Yii;
use app\models\TimeslotModel;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TimeslotModelController implements the CRUD actions for TimeslotModel model.
 */
class TimeslotModelController extends Controller
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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],

            ],
        ];
    }

    public function actionGenerate(){
        //set the value of interval for the creation of timeslot
        $scope = new \DateInterval('P2M');

        TimeslotModel::generateNextTimeslot( date_add(new \DateTime(), $scope ));

        return $this->redirect('index', []);
    }

    /**
     * Lists all TimeslotModel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TimeslotModel::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TimeslotModel model.
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
     * Creates a new TimeslotModel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TimeslotModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->advanceModelGeneration( new \DateTime($model->start_validity . '+ 3 months') );
            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            $simulators = new ActiveDataProvider([
                'query' => Simulator::find(),
            ]);

            return $this->render('create', [
                'model' => $model,
                'simulators' => $simulators->getModels()
            ]);
        }

    }

    /**
     * Updates an existing TimeslotModel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->updateModel(Yii::$app->request->post())) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            $simulators = new ActiveDataProvider([
                'query' => Simulator::find(),
            ]);

            return $this->render('update', [
                'model' => $model,
                'simulators' => $simulators->getModels()
            ]);
        }
    }

    /**
     * Deletes an existing TimeslotModel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteModel();

        return $this->redirect(['index']);
    }

    public static function weekdays()
    {
        return [
            Yii::t('app', 'Monday'),
            Yii::t('app', 'Tuesday'),
            Yii::t('app', 'Wednesday'),
            Yii::t('app', 'Thursday'),
            Yii::t('app', 'Friday'),
            Yii::t('app', 'Saturday'),
            Yii::t('app', 'Sunday')
        ];
    }


    /**
     * Finds the TimeslotModel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TimeslotModel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TimeslotModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
