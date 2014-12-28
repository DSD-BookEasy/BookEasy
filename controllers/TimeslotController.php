<?php

namespace app\controllers;

use app\models\Simulator;
use Yii;
use app\models\Timeslot;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manageTimeslots']
                    ],
                ],

            ],
        ];
    }

    /**
     * Lists all Timeslot models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Timeslot::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
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
     * Ajax action for fullcalendar AJAX source returning anonymous data
     * @param $simulator the id of the simulator to show the timeslots of
     * @param string $start the starting date of timeslots to show. Must be encoded in ISO8601
     * @param string $end the ending date of timeslots to show. Must be encoded in ISO8601
     * @param bool $background whether the timeslot should be rendered as background ones
     * @return string
     */
    public function actionAnonCalendar($simulator, $start = null, $end = null, $background = false){
        $timeslots=[];
        if(!empty($simulator) and is_numeric($simulator)){
            try {
                $s = new \DateTime($start);
                $e = new \DateTime($end);

                $timeslots = Timeslot::find()->
                where(['id_simulator' => $simulator])->
                andWhere(['>=', 'start', $s->format("c")])->
                andWhere(['<=', 'end', $e->format("c")])->all();
            }
            catch(\Exception $e){
                //Invalid dates. Return empty timeslots
            }

            $out=[];

            foreach($timeslots as $t){
                $out[]=[
                    'id' => $t->id,
                    'title' => $t->id_simulator? Yii::t('app','Unavailable') : Yii::t('app','Available'),
                    'allDay' => false,
                    'start' => $t->start,
                    'end' => $t->end,
                    'className' => $t->id_simulator? 'unavailable' : 'available',
                    'rendering' => $background? 'background' : null
                ];
            }

            $resp = Yii::$app->response;
            $resp->data = $out;
            $resp->format = Response::FORMAT_JSON;
        }
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

            $simulators = new ActiveDataProvider([
                'query' => Simulator::find(),
            ]);

            return $this->render('create', [
                'model' => $model,
                'simulators' => $simulators->getModels(),
            ]);
        }
    }

    /**
     * Updates an existing Timeslot model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $goTo = null)
    {
        $model = $this->findModel($id);

        if($goTo != null){
            Yii::$app->user->setReturnUrl($goTo);
        }else{
            Yii::$app->user->setReturnUrl(Url::to(['view', 'id' => $model->id]));
        }

        if ($model->load(Yii::$app->request->post())){
            $model->id_timeSlotModel = NULL;
            if($model->save()){
                return $this->goBack();
            }
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
}
