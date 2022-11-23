<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%order_delivery_in_yandex}}".
 *
 * @property int $id
 * @property int $order_id Order
 * @property int $yandex_id Yandex id
 * @property int $shop_id Shop
 * @property string|null $shop_address Shop address
 * @property string|null $shop_latitude Shop latitude
 * @property string|null $shop_longitude Shop longitude
 * @property string|null $client_address Client address
 * @property string|null $client_latitude Client latitude
 * @property string|null $client_longitude Client longitude
 * @property string|null $tariff Tariff
 * @property float|null $offer_price Offer price
 * @property float|null $final_price Final price
 * @property string|null $driver_phone Driver phone
 * @property string|null $driver_phone_ext Driver phone ext
 * @property int $user_id User
 * @property string|null $status Status
 * @property int $created_at Created date
 * @property int $updated_at Updated date
 *
 * @property Order $order
 * @property Shop $shop
 * @property User $user
 */
class OrderDeliveryInYandex extends \yii\db\ActiveRecord
{
    const YANDEX_TARIFF_COURIER = 'courier';
    const YANDEX_TARIFF_EXPRESS = 'express';
    const YANDEX_TARIFF_CARGO = 'cargo';

    const YANDEX_STATUS_ACCEPTED = 'accepted';
    const YANDEX_STATUS_CANCELLED = 'cancelled';
    const YANDEX_STATUS_CANCELLED_BY_TAXI = 'cancelled_by_taxi';
    const YANDEX_STATUS_CANCELLED_WITH_ITEMS_ON_HANDS = 'cancelled_with_items_on_hands';
    const YANDEX_STATUS_CANCELLED_WITH_PAYMENT = 'cancelled_with_payment';
    const YANDEX_STATUS_DELIVERED = 'delivered';
    const YANDEX_STATUS_DELIVERED_FINISH = 'delivered_finish';
    const YANDEX_STATUS_DELIVERY_ARRIVED = 'delivery_arrived';
    const YANDEX_STATUS_ESTIMATING = 'estimating';
    const YANDEX_STATUS_ESTIMATING_FAILED = 'estimating_failed';
    const YANDEX_STATUS_FAILED = 'failed';
    const YANDEX_STATUS_NEW = 'new';
    const YANDEX_STATUS_PERFORMER_DRAFT = 'performer_draft';
    const YANDEX_STATUS_PERFORMER_FOUND = 'performer_found';
    const YANDEX_STATUS_PERFORMER_LOOKUP = 'performer_lookup';
    const YANDEX_STATUS_PERFORMER_NOT_FOUND = 'performer_not_found';
    const YANDEX_STATUS_PICKUP_ARRIVED = 'pickup_arrived';
    const YANDEX_STATUS_PICKUPED = 'pickuped';
    const YANDEX_STATUS_READY_FOR_APPROVAL = 'ready_for_approval';
    const YANDEX_STATUS_READY_FOR_DELIVERY_CONFIRMATION = 'ready_for_delivery_confirmation';
    const YANDEX_STATUS_READY_FOR_PICKUP_CONFIRMATION = 'ready_for_pickup_confirmation';
    const YANDEX_STATUS_READY_FOR_RETURN_CONFIRMATION = 'ready_for_return_confirmation';
    const YANDEX_STATUS_RETURN_ARRIVED = 'return_arrived';
    const YANDEX_STATUS_RETURNED = 'returned';
    const YANDEX_STATUS_RETURNED_FINISH = 'returned_finish';
    const YANDEX_STATUS_RETURNING = 'returning';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order_delivery_in_yandex}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'yandex_id', 'shop_id', 'user_id'], 'required'],
            [['order_id', 'shop_id', 'user_id', 'created_at', 'updated_at'], 'integer'],
            [['offer_price', 'final_price'], 'number'],
            [['shop_address', 'client_address'], 'string', 'max' => 500],
            [['yandex_id', 'shop_latitude', 'shop_longitude', 'client_latitude', 'client_longitude'], 'string', 'max' => 100],
            [['tariff'], 'string', 'max' => 50],
            [['driver_phone', 'driver_phone_ext'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['shop_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::className(), 'targetAttribute' => ['shop_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['tariff', 'in', 'range' => array_keys($this->getYandexTariffs())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'order_id' => Yii::t('order', 'Order'),
            'yandex_id' => Yii::t('order', 'Yandex id'),
            'shop_id' => Yii::t('shop', 'Shop'),
            'shop_address' => Yii::t('order', 'Shop address'),
            'shop_latitude' => Yii::t('order', 'Shop latitude'),
            'shop_longitude' => Yii::t('order', 'Shop longitude'),
            'client_address' => Yii::t('order', 'Client address'),
            'client_latitude' => Yii::t('order', 'Client latitude'),
            'client_longitude' => Yii::t('order', 'Client longitude'),
            'tariff' => Yii::t('order', 'Tariff'),
            'offer_price' => Yii::t('order', 'Offer price'),
            'final_price' => Yii::t('order', 'Final price'),
            'driver_phone' => Yii::t('order', 'Driver phone'),
            'driver_phone_ext' => Yii::t('order', 'Driver phone ext'),
            'user_id' => Yii::t('order', 'User'),
            'status' => Yii::t('backend', 'Status'),
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

    /**
     * Gets query for [[Shop]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShop()
    {
        return $this->hasOne(Shop::className(), ['id' => 'shop_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return array
     */
    public static function getYandexTariffs()
    {
        return [
            self::YANDEX_TARIFF_COURIER => Yii::t('order', 'Courier'),
            self::YANDEX_TARIFF_EXPRESS => Yii::t('order', 'Express'),
            self::YANDEX_TARIFF_CARGO => Yii::t('order', 'Cargo'),
        ];
    }

    public function getYandexStatus()
    {
        return ArrayHelper::getValue(self::getYandexStatuses(), $this->status);
    }

    /**
     * @return array
     */
    public static function getYandexStatuses()
    {
        return [
            self::YANDEX_STATUS_ACCEPTED => Yii::t('order', 'The application is confirmed by the client.'),
            self::YANDEX_STATUS_CANCELLED => Yii::t('order', 'The order was canceled by the customer free of charge.'),
            self::YANDEX_STATUS_CANCELLED_BY_TAXI => Yii::t('order', 'The driver canceled the order (before receiving the cargo).'),
            self::YANDEX_STATUS_CANCELLED_WITH_ITEMS_ON_HANDS => Yii::t('order', 'The client canceled the request for a fee without the need to return the cargo (the request was created with the optional_return flag).'),
            self::YANDEX_STATUS_CANCELLED_WITH_PAYMENT => Yii::t('order', 'The order was canceled by the client for a fee (the driver has already arrived).'),
            self::YANDEX_STATUS_DELIVERED => Yii::t('order', 'The driver successfully delivered the goods.'),
            self::YANDEX_STATUS_DELIVERED_FINISH => Yii::t('order', 'Order completed.'),
            self::YANDEX_STATUS_DELIVERY_ARRIVED => Yii::t('order', 'The driver arrived at point B.'),
            self::YANDEX_STATUS_ESTIMATING => Yii::t('order', 'The process of evaluating the application is underway (selection of the type of car according to the parameters of the cargo and calculation of the cost).'),
            self::YANDEX_STATUS_ESTIMATING_FAILED => Yii::t('order', 'Failed to evaluate application. The reason can be seen in the error_messages in the /info operation response.'),
            self::YANDEX_STATUS_FAILED => Yii::t('order', 'An error occurred during the execution of the order, further execution is impossible.'),
            self::YANDEX_STATUS_NEW => Yii::t('order', 'New application.'),
            self::YANDEX_STATUS_PERFORMER_DRAFT => Yii::t('order', 'The driver is being searched.'),
            self::YANDEX_STATUS_PERFORMER_FOUND => Yii::t('order', 'The driver has been found and is driving to point A.'),
            self::YANDEX_STATUS_PERFORMER_LOOKUP => Yii::t('order', 'The application has been processed. Intermediate status before creating an order.'),
            self::YANDEX_STATUS_PERFORMER_NOT_FOUND => Yii::t('order', 'Could not find the driver. You can try again after a while.'),
            self::YANDEX_STATUS_PICKUP_ARRIVED => Yii::t('order', 'The driver arrived at point A.'),
            self::YANDEX_STATUS_PICKUPED => Yii::t('order', 'The driver successfully picked up the cargo.'),
            self::YANDEX_STATUS_READY_FOR_APPROVAL => Yii::t('order', 'The application has been successfully evaluated and is awaiting confirmation from the client.'),
            self::YANDEX_STATUS_READY_FOR_DELIVERY_CONFIRMATION => Yii::t('order', 'The driver is waiting for the recipient to tell him the confirmation code.'),
            self::YANDEX_STATUS_READY_FOR_PICKUP_CONFIRMATION => Yii::t('order', 'The driver is waiting for the sender to tell him the confirmation code.'),
            self::YANDEX_STATUS_READY_FOR_RETURN_CONFIRMATION => Yii::t('order', 'The driver at the return point is waiting for the confirmation code to be called to him.'),
            self::YANDEX_STATUS_RETURN_ARRIVED => Yii::t('order', 'The driver arrived at the return point.'),
            self::YANDEX_STATUS_RETURNED => Yii::t('order', 'The driver successfully returned the cargo.'),
            self::YANDEX_STATUS_RETURNED_FINISH => Yii::t('order', 'Order completed.'),
            self::YANDEX_STATUS_RETURNING => Yii::t('order', 'The driver had to return the cargo and he goes to the return point.'),
        ];
    }
}
