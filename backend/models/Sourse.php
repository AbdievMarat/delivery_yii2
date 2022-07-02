<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%sourses}}".
 *
 * @property int $id
 * @property string $name Name
 * @property int $status Status
 */
class Sourse extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%sourses}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('sourse', 'ID'),
            'name' => Yii::t('sourse', 'Name'),
            'status' => Yii::t('sourse', 'Status'),
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
