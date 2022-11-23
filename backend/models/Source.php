<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%sources}}".
 *
 * @property int $id
 * @property string $name Name
 * @property int $status Status
 */
class Source extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%sources}}';
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
            ['status', 'in', 'range' => array_keys($this->getStatuses())],
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
            'status' => Yii::t('backend', 'Status'),
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
     * list of sources for filters selects
     *
     * @return array
     */
    public static function getSourcesList()
    {
        $list = static::find()->select('id, name')->where(['status' => self::STATUS_ACTIVE])->all();
        return ArrayHelper::map($list, 'id', 'name');
    }
}
