<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%shops}}".
 *
 * @property int $id
 * @property int $country_id Country
 * @property string $name Name
 * @property string|null $contact_phone Contact phone
 * @property string $address Address
 * @property string $latitude Latitude
 * @property string $longitude Longitude
 * @property string|null $mobile_backend_id Mobile backend id
 * @property int $status Status
 *
 * @property Country $country
 */
class Shop extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shops}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'name', 'address', 'latitude', 'longitude'], 'required'],
            [['country_id', 'status'], 'integer'],
            [['name', 'contact_phone', 'mobile_backend_id'], 'string', 'max' => 255],
            [['address'], 'string', 'max' => 500],
            [['latitude', 'longitude'], 'string', 'max' => 100],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['mobile_backend_id'], 'unique', 'filter' => 'status = 1'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'ID'),
            'country_id' => Yii::t('shop', 'Country'),
            'name' => Yii::t('shop', 'Name'),
            'contact_phone' => Yii::t('shop', 'Contact phone'),
            'address' => Yii::t('shop', 'Address'),
            'latitude' => Yii::t('shop', 'Latitude'),
            'longitude' => Yii::t('shop', 'Longitude'),
            'mobile_backend_id' => Yii::t('shop', 'Mobile backend id'),
            'status' => Yii::t('shop', 'Status'),
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
    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('backend', 'Active'),
            self::STATUS_INACTIVE => Yii::t('backend', 'Inactive'),
        ];
    }
}
