<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%sourses}}".
 *
 * @property int $id
 * @property string $name Name
 * @property int|null $availability Availability
 */
class Sourse extends \yii\db\ActiveRecord
{
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
            [['availability'], 'integer'],
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
            'availability' => Yii::t('sourse', 'Availability'),
        ];
    }
}
