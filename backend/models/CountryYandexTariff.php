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
    const TARIFF_COURIER = 'courier';
    const TARIFF_EXPRESS = 'express';
    const TARIFF_CARGO = 'cargo';

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
            ['name_tariff', 'in', 'range' => array_keys($this->getTariffs())],
            /*['name_tariff', 'unique', 'targetAttribute' => ['name_tariff', 'country_id']]
            ['name_tariff', 'unique', 'filter' => function($attribute) {
                if(
                    (self::find()
                    ->where(['name_tariff' => $this->name_tariff])
                    ->andWhere(['country_id' => $this->country_id])
                    ->count())>1
                ) {
                    $this->addError('name_tariff', 'Social security number already exists in the database.');
                }
            }],*/
        ];
    }

    public function normalizePhone($value) {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
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

    /**
     * @return array
     */
    public static function getTariffs()
    {
        return [
            self::TARIFF_COURIER => Yii::t('country_yandex_tariffs', 'Courier'),
            self::TARIFF_EXPRESS => Yii::t('country_yandex_tariffs', 'Express'),
            self::TARIFF_CARGO => Yii::t('country_yandex_tariffs', 'Cargo'),
        ];
    }
}
