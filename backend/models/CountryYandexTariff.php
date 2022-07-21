<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%country_yandex_tariffs}}".
 *
 * @property int $id
 * @property int $country_id Country
 * @property string $name_tariff Name tariff
 *
 * @property Country $country
 */
class CountryYandexTariff extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%country_yandex_tariffs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_tariff'], 'required'],
            [['country_id'], 'integer'],
            [['name_tariff'], 'string', 'max' => 255],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('country_yandex_tariffs', 'ID'),
            'country_id' => Yii::t('country_yandex_tariffs', 'Country'),
            'name_tariff' => Yii::t('country_yandex_tariffs', 'Name tariff'),
        ];
    }

    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }
}
