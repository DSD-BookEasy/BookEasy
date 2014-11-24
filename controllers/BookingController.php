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
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateWeekdays()
    {
        $model = new Booking();
        $timeSlots = [];

        if(Yii::$app->request->post($name = 'timeslot') && !isset(Yii::$app->session['timeslots'])){
            TimeSlot::loadMultiple($timeSlots, Yii::$app->request->post($name = 'timeslots'));
            Yii::app()->session['timeslots'] = $timeSlots;
        }elseif(!isset(Yii::app()->session['timeslots'])){
            $this -> goBack();
        }

        if ($model->load(Yii::$app->request->post($name = 'Booking'))) {
            //lock
            $transaction = Yii::$app->db->beginTransaction(\yii\db\Transaction::SERIALIZABLE);
            try {
                $timeSlots = Yii::app()->session['timeslots'];
                foreach ($timeSlots as $slot) {
                    foreach ($timeSlots as $slot2) {
                        if ($slot != $slot2) {
                            if ($slot->overlapping($slot2)) {
                                //rise error
                                throw new ErrorException();
                            }
                        }
                    }
                    if ($slot->checkConsistency()) {
                        //rise error
                        throw new ErrorException;
                    }
                }

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
            }catch(Exception $e){
                $transaction->rollBack();
            }
            $transaction->commit();

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('createWeekdays', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new Booking model for sunday.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Booking();
        $timeSlots = [];

        if(Yii::$app->request->post($name = 'timeslot') && !isset(Yii::$app->session['timeslots'])){
            TimeSlot::loadMultiple($timeSlots, Yii::$app->request->post($name = 'timeslots'));
            Yii::app()->session['timeslots'] = $timeSlots;
        }elseif(!isset(Yii::app()->session['timeslots'])){
            $this -> goBack();
        }

        if ($model->load(Yii::$app->request->post($name = 'Booking'))) {
            //lock
            $transaction = Yii::$app->db->beginTransaction(\yii\db\Transaction::SERIALIZABLE);
            try {
                $timeSlots = Yii::app()->session['timeslots'];
                foreach ($timeSlots as $slot) {
                    foreach ($timeSlots as $slot2) {
                        if ($slot != $slot2) {
                            if ($slot->overlapping($slot2)) {
                                //rise error
                                throw new ErrorException();
                            }
                        }
                    }
                    if ($slot->checkConsistency($slot->id)) {
                        //rise error
                        throw new ErrorException;
                    }
                }

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
            }catch(Exception $e){
                $transaction->rollBack();
            }
            $transaction->commit();

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('createWeekdays', [
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
}
