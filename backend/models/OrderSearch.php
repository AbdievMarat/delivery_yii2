<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Order;

/**
 * OrderSearch represents the model behind the search form of `backend\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'country_id', 'payment_status', 'operator_deadline_date', 'operator_real_date', 'user_id_operator', 'shop_manager_deadline_date', 'shop_manager_real_date', 'user_id_shop_manager', 'driver_deadline_date', 'driver_real_date', 'user_id_driver', 'shop_id', 'delivery_type', 'delivery_date', 'status', 'source_id', 'created_at', 'updated_at'], 'integer'],
            [['mobile_backend_id', 'mobile_backend_callback_url', 'address', 'latitude', 'longitude', 'entrance', 'floor', 'flat', 'comment_for_operator', 'comment_for_shop_manager', 'comment_for_driver'], 'safe'],
            [['order_price', 'payment_cash', 'payment_bonuses'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Order::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'country_id' => $this->country_id,
            'order_price' => $this->order_price,
            'payment_cash' => $this->payment_cash,
            'payment_bonuses' => $this->payment_bonuses,
            'payment_status' => $this->payment_status,
            'operator_deadline_date' => $this->operator_deadline_date,
            'operator_real_date' => $this->operator_real_date,
            'user_id_operator' => $this->user_id_operator,
            'shop_manager_deadline_date' => $this->shop_manager_deadline_date,
            'shop_manager_real_date' => $this->shop_manager_real_date,
            'user_id_shop_manager' => $this->user_id_shop_manager,
            'driver_deadline_date' => $this->driver_deadline_date,
            'driver_real_date' => $this->driver_real_date,
            'user_id_driver' => $this->user_id_driver,
            'shop_id' => $this->shop_id,
            'delivery_type' => $this->delivery_type,
            'delivery_date' => $this->delivery_date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'mobile_backend_id', $this->mobile_backend_id])
            ->andFilterWhere(['like', 'mobile_backend_callback_url', $this->mobile_backend_callback_url])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'latitude', $this->latitude])
            ->andFilterWhere(['like', 'longitude', $this->longitude])
            ->andFilterWhere(['like', 'entrance', $this->entrance])
            ->andFilterWhere(['like', 'floor', $this->floor])
            ->andFilterWhere(['like', 'flat', $this->flat])
            ->andFilterWhere(['like', 'comment_for_operator', $this->comment_for_operator])
            ->andFilterWhere(['like', 'comment_for_shop_manager', $this->comment_for_shop_manager])
            ->andFilterWhere(['like', 'comment_for_driver', $this->comment_for_driver]);

        return $dataProvider;
    }
}
