<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%countries}}".
 *
 * @property int $id
 * @property string $name Name
 * @property string|null $name_currency Name currency
 * @property string|null $currency_iso Currency ISO
 * @property string|null $name_organization Name organization
 * @property string|null $contact_phone Contact phone
 * @property string|null $token_yandex Token yandex
 * @property string|null $token_mobile_backend Token mobile backend
 * @property string|null $latitude Latitude
 * @property string|null $longitude Longitude
 * @property string|null $yandex_tariffs Yandex tariffs
 * @property int $status Status
 * @property string|null $address Address
 */
class Country extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%countries}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'address', 'latitude', 'longitude'], 'required'],
            [['status'], 'integer'],
            [['name', 'name_currency', 'name_organization', 'contact_phone', 'token_yandex', 'token_mobile_backend'], 'string', 'max' => 255],
            [['latitude', 'longitude'], 'string', 'max' => 100],
            [['address'], 'string', 'max' => 500],
            [['currency_iso'], 'string', 'min' => 3, 'max' => 3],
            ['status', 'in', 'range' => array_keys($this->getStatuses())],
            [['yandex_tariffs'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'name' => Yii::t('backend', 'Name'),
            'name_currency' => Yii::t('country', 'Name Currency'),
            'currency_iso' => Yii::t('country', 'Currency ISO'),
            'name_organization' => Yii::t('country', 'Name Organization'),
            'contact_phone' => Yii::t('country', 'Contact Phone'),
            'token_yandex' => Yii::t('country', 'Token Yandex'),
            'token_mobile_backend' => Yii::t('country', 'Token Mobile Backend'),
            'latitude' => Yii::t('country', 'Latitude'),
            'longitude' => Yii::t('country', 'Longitude'),
            'yandex_tariffs' => Yii::t('country', 'Yandex tariffs'),
            'status' => Yii::t('backend', 'Status'),
            'address' => Yii::t('country', 'Address'),
        ];
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('backend', 'Active'),
            self::STATUS_INACTIVE => Yii::t('backend', 'Inactive'),
        ];
    }

    /**
     * Gets query for [[Shop]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShops()
    {
        return $this->hasMany(Shop::className(), ['country_id' => 'id'])->andWhere(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * list of countries for filters selects
     *
     * @return array
     */
    public static function getCountriesList()
    {
        $list = static::find()->select('id, name')->where(['status' => self::STATUS_ACTIVE])->all();
        return ArrayHelper::map($list, 'id', 'name');
    }

    /**
     *  list of available countries to the user for filters selects
     *
     * @return array
     */
    public static function getAvailableCountriesToUser()
    {
        $list = static::find()->select('id, name')->where(['id' => explode(',', Yii::$app->user->identity->available_countries)])->all();
        return ArrayHelper::map($list, 'id', 'name');
    }
}