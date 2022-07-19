<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Country;

/**
 * CountrySearch represents the model behind the search form of `backend\models\Country`.
 */
class CountrySearch extends Country
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['name', 'name_currency', 'name_organization', 'contact_phone', 'token_yandex', 'token_mobile_backend', 'latitude', 'longitude', 'address'], 'safe'],
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
        $query = Country::find();

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
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'name_currency', $this->name_currency])
            ->andFilterWhere(['like', 'name_organization', $this->name_organization])
            ->andFilterWhere(['like', 'contact_phone', $this->contact_phone])
            ->andFilterWhere(['like', 'token_yandex', $this->token_yandex])
            ->andFilterWhere(['like', 'token_mobile_backend', $this->token_mobile_backend])
            ->andFilterWhere(['like', 'latitude', $this->latitude])
            ->andFilterWhere(['like', 'longitude', $this->longitude])
            ->andFilterWhere(['like', 'address', $this->address]);

        return $dataProvider;
    }
}
