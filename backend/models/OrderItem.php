<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "{{%order_items}}".
 *
 * @property int $id
 * @property int $order_id Order
 * @property string $product_code Product code
 * @property string $product_name Product name
 * @property float|null $product_price Product price
 * @property int $amount Amount
 * @property int $created_at Created date
 * @property int $updated_at Updated date
 *
 * @property Order $order
 */
class OrderItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order_items}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_code', 'product_name', 'amount'], 'required'],
            [['order_id', 'created_at', 'updated_at'], 'integer'],
            [['product_price'], 'number', 'min' => 1],
            [['product_code', 'product_name'], 'string', 'max' => 255],
            [['amount'], 'integer', 'min' => 1],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'order_id' => Yii::t('order_item', 'Order'),
            'product_code' => Yii::t('order_item', 'Product code'),
            'product_name' => Yii::t('order_item', 'Product name'),
            'product_price' => Yii::t('order_item', 'Product price'),
            'amount' => Yii::t('order_item', 'Amount'),
            'created_at' => Yii::t('backend', 'Created date'),
            'updated_at' => Yii::t('backend', 'Updated date'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
}
