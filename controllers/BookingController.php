<?php

namespace app\controllers;

use app\models\TimeSlot;
use Yii;
use app\models\Booking;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BookingController implements the CRUD actions for Booking model.
 */
class BookingController extends Controller
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

    public function actionSearch(){

        $model = new Booking();
        if ( (Yii::$app->request->post())) {
            $id = (Yii::$app->request->post($name = 'Booking'));
            return $this->redirect(['view',
                'id' => $id['id'],
            ]);
        } else {
            return $this->render('search', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Lists all Booking models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Booking::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Booking model.
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
     * Creates a new Booking model for weekdays.
     * Expects to receive timeSlots in the POST "timeslot" field, as in the standard Yii format
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateWeekdays()
    {
        $model = new Booking();
        $timeSlots = [];

        if(Yii::$app->request->post($name = 'timeslot') && !isset(Yii::$app->session['timeslots'])){
            TimeSlot::loadMultiple($timeSlots, Yii::$app->request->post($name = 'timeslots'));
            foreach($timeSlots as $timeSlot){
                if(!empty($timeSlot->id)){
                    throw new ErrorException();
                }
            }
            Yii::$app->session['timeslots'] = $timeSlots;
        }elseif(!isset(Yii::$app->session['timeslots'])){
            $this -> goBack();
        }

        if ($model->load(Yii::$app->request->post($name = 'Booking'))) {
            //lock
            $transaction = Yii::$app->db->beginTransaction(\yii\db\Transaction::SERIALIZABLE);
            try {
                $timeSlots = Yii::$app->session['timeslots'];

                if (!$model->save()) {
                    //rise error
                    throw new ErrorException();
                }

                foreach ($timeSlots as $slot) {
                    $slot->id_booking = $model->id;
                    if (!$slot->save()) {
                        //rise error
                        throw new ErrorException();
                    }
                }

                $transaction->commit();
                $this->notifyCoordinators($model);
                return $this->redirect(['view', 'id' => $model->id]);
            }catch(Exception $e){
                $transaction->rollBack();
                throw new ErrorException();
            }
        } else {
            return $this->render('createWeekdays', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new Booking model for sunday.
     * Expects an array of timeSlot ids in the POST "timeslot" field
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Booking();

        if(Yii::$app->request->post($name = 'timeslot') && !isset(Yii::$app->session['timeslots'])){
            $timeSlots=(array)Yii::$app->request->post($name = 'timeslots');
            //Accept only an array of integer values
            foreach($timeSlots as $timeSlot){
                if(!is_numeric($timeSlot) or ((int)$timeSlot)!=$timeSlot or $timeSlot<=0){
                    throw new ErrorException();
                }
            }

            //And save them in the session
            Yii::$app->session['timeslots'] = TimeSlot::findAll(Yii::$app->request->post($name = 'timeslots'));
        }elseif(!isset(Yii::$app->session['timeslots'])){
            $this -> goBack();
        }

        if ($model->load(Yii::$app->request->post($name = 'Booking'))) {
            //lock
            $transaction = Yii::$app->db->beginTransaction(\yii\db\Transaction::SERIALIZABLE);
            try {
                $timeSlots = Yii::$app->session['timeslots'];

                if (!$model->save()) {//Note: does the framework automatically update the id on insert?
                    //rise error
                    throw new ErrorException();
                }

                foreach ($timeSlots as $slot) {
                    $slot->id_booking = $model->id;
                    if (!$slot->save()) {
                        //rise error
                        throw new ErrorException();
                    }
                }

                $transaction->commit();
                $this->notifyCoordinators($model);
                return $this->redirect(['view', 'id' => $model->id]);
            }catch(Exception $e){
                $transaction->rollBack();
                //TODO here we should go to error page
                throw new ErrorException();
            }

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Booking model.
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
     * Deletes an existing Booking model.
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
     * Finds the Booking model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Booking the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Booking::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function notifyCoordinators($booking){
        $mails=[];
        //TODO find Coordinators emails

        Yii::$app->mailer->compose()
            ->setFrom(\Yii::$app->params['adminEmail'])
            ->setBcc($mails)
            ->setSubject(\Yii::t('app','New Booking'))
            ->setTextBody(\Yii::t('app',"Hello,\n\na new booking for the museum simulators has been received. Check
            it out here:\n".\yii\helpers\Url::to(['booking/view','id'=>$booking->id])))
            ->setHtmlBody('<p>Hello,</p><p>a new booking for the museum simulators has been received. Check
            it out by clicking <a href="'.\yii\helpers\Url::to(['booking/view','id'=>$booking->id]).'">here</a></p>')
            ->send();
    }
}
