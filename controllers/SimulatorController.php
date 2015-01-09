<?php

namespace app\controllers;

use app\models\Timeslot;
use DateTime;
use Yii;
use app\models\Simulator;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;



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
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','view','create','update','delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manageSimulator']
                    ],
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

        if (Yii::$app->request->isPost) {

            if ($model->load(Yii::$app->request->post()) && $model->save()) {

                $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');

                // Check whether the user did upload a file and validate to be sure it is an image
                if ($model->uploadFile && $model->validate()) {
                    $model->uploadImage();
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }

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

        if (Yii::$app->request->isPost) {

            if ($model->load(Yii::$app->request->post()) && $model->save()) {

                if ( Yii::$app->request->post('del_image') ) {
                    $model->removeImages();
                }

                $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');

                // Check whether the user did upload a file and validate to be sure it is an image
                if ($model->uploadFile && $model->validate()) {

                    // Since we're allowing only one image for simulator, we delete the other ones to keep it clean
                    if ( $model->getImage() ) {
                        $model->removeImages();
                    }

                    $model->uploadImage();
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }

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

        $simId = \Yii::$app->request->get("id");

        if (empty($simId) || ((int)$simId) == 0) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $week = \Yii::$app->request->get("week");

        if (empty($week) || !strtotime($week)) {
            // If the week is not set (properly), it's the today's one
            $week = date(DateTime::ISO8601);
        }

        // Set the current week
        $currWeek = new DateTime($week);
        $currWeek->modify('Thursday this week');

        // and the week before it
        $prevWeek = clone $currWeek;
        $prevWeek->modify("previous Thursday");

        // and the week after it
        $nextWeek = clone $currWeek;
        $nextWeek->modify("next Thursday");
        // Why are we using all this Thursdays? This is caused by ISO8601's definition of the first week of the year
        // ("the week with the year's first Thursday in it") and should solve bugs related to the transition between years


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
            'week' => $currWeek,
            'currWeek' => $currWeek->format("Y\WW"),
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
