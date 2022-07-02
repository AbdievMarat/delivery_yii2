<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%counties}}".
 *
 * @property int $id
 * @property string $name Name
 * @property string|null $name_currency Name currency
 * @property string|null $name_organization Name organization
 * @property string|null $contact_phone Contact phone
 * @property string|null $token_yandex Token yandex
 * @property string|null $token_mobile_backend Token mobile backend
 * @property string|null $latitude Latitude
 * @property string|null $longitude Longitude
 * @property int $status Status
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
        return '{{%counties}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status'], 'integer'],
            [['name', 'name_currency', 'name_organization', 'contact_phone', 'token_yandex', 'token_mobile_backend'], 'string', 'max' => 255],
            [['latitude', 'longitude'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('country', 'ID'),
            'name' => Yii::t('country', 'Name'),
            'name_currency' => Yii::t('country', 'Name Currency'),
            'name_organization' => Yii::t('country', 'Name Organization'),
            'contact_phone' => Yii::t('country', 'Contact Phone'),
            'token_yandex' => Yii::t('country', 'Token Yandex'),
            'token_mobile_backend' => Yii::t('country', 'Token Mobile Backend'),
            'latitude' => Yii::t('country', 'Latitude'),
            'longitude' => Yii::t('country', 'Longitude'),
            'status' => Yii::t('country', 'Status'),
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
}
