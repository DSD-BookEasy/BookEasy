<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Timeslot;

/**
 * TimeslotSearch represents the model behind the search form about `app\models\Timeslot`.
 */
class TimeslotSearch extends Timeslot
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cost', 'id_timeSlotModel', 'id_simulator', 'id_booking'], 'integer'],
            [['start', 'end'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Timeslot::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'start' => $this->start,
            'end' => $this->end,
            'cost' => $this->cost,
            'id_timeSlotModel' => $this->id_timeSlotModel,
            'id_simulator' => $this->id_simulator,
            'id_booking' => $this->id_booking,
        ]);

        return $dataProvider;
    }
}
