<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%orders}}".
 *
 * @property int $id
 * @property string|null $mobile_backend_id Mobile backend id
 * @property string|null $mobile_backend_callback_url Mobile backend callback url
 * @property int $client_phone Client phone
 * @property int $client_name Client name
 * @property int $country_id Country
 * @property string|null $address Address
 * @property string|null $latitude Latitude
 * @property string|null $longitude Longitude
 * @property string|null $entrance Entrance
 * @property string|null $floor Floor
 * @property string|null $flat Flat
 * @property float|null $order_price Order price
 * @property float|null $payment_cash Payment cash
 * @property float|null $payment_bonuses Payment bonuses
 * @property int $payment_status Payment status
 * @property string|null $comment_for_operator Comment for operator
 * @property int|null $operator_deadline_date Operator deadline date
 * @property int|null $operator_real_date Operator real date
 * @property int|null $user_id_operator Operator
 * @property string|null $comment_for_shop_manager Comment for shop manager
 * @property int|null $shop_manager_deadline_date Shop manager deadline date
 * @property int|null $shop_manager_real_date Shop manager real date
 * @property int|null $user_id_shop_manager Shop manager
 * @property string|null $comment_for_driver Comment for driver
 * @property int|null $driver_deadline_date Driver deadline date
 * @property int|null $driver_real_date Driver real date
 * @property int|null $user_id_driver Driver
 * @property int|null $shop_id Shop
 * @property int|null $source_id Source
 * @property int|null $delivery_type Delivery type
 * @property int|null $delivery_date Delivery date
 * @property int $status Status
 * @property int $created_at Created date
 * @property int $updated_at Updated date
 *
 * @property Country $country
 * @property User $driverUser
 * @property User $operatorUser
 * @property OrderItem[] $orderItems
 * @property Shop $shop
 * @property Source $source
 * @property User $shopManagerUser
 * @property array $informationAboutDeadline
 */
class Order extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_IN_SHOP = 2;
    const STATUS_AT_DRIVER = 3;
    const STATUS_DELIVERED = 4;
    const STATUS_CANCELED = 5;
    const STATUS_GIVEN_TO_KDK_DELIVERY = 6;

    const PAYMENT_STATUS_PAID = 1;
    const PAYMENT_STATUS_NOT_PAID = 2;

    const TYPE_DELIVERY_SOON_AS_POSSIBLE = 1;
    const TYPE_DELIVERY_DATE = 2;

    const OPERATOR_DEADLINE_IN_MINUTES = 16;
    const SHOP_DEADLINE_IN_MINUTES = 18;
    const DRIVER_DEADLINE_IN_MINUTES = 21;

    public array $informationAboutDeadline = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%orders}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_phone', 'country_id', 'client_name', 'address'], 'required'],
            [['country_id', 'payment_status', 'operator_deadline_date', 'operator_real_date', 'user_id_operator', 'shop_manager_deadline_date', 'shop_manager_real_date', 'user_id_shop_manager', 'driver_deadline_date', 'driver_real_date', 'user_id_driver', 'shop_id', 'source_id', 'delivery_type', 'delivery_date', 'status', 'created_at', 'updated_at'], 'integer'],
            [['order_price', 'payment_cash', 'payment_bonuses'], 'number'],
            [['order_price'], 'number', 'integerOnly' => false, 'min' => '1'],
            [['mobile_backend_id', 'mobile_backend_callback_url', 'client_name'], 'string', 'max' => 255],
            [['address', 'comment_for_operator', 'comment_for_shop_manager', 'comment_for_driver'], 'string', 'max' => 500],
            [['latitude', 'longitude', 'entrance', 'floor', 'flat'], 'string', 'max' => 100],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['user_id_driver'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id_driver' => 'id']],
            [['user_id_operator'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id_operator' => 'id']],
            [['shop_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::className(), 'targetAttribute' => ['shop_id' => 'id']],
            [['source_id'], 'exist', 'skipOnError' => true, 'targetClass' => Source::className(), 'targetAttribute' => ['source_id' => 'id']],
            [['user_id_shop_manager'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id_shop_manager' => 'id']],
            ['status', 'in', 'range' => array_keys($this->getStatuses())],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['shop_id', 'required', 'on' => 'delivery'],
            ['id', 'notExistsItems', 'on' => 'delivery'],
        ];
    }

    /**
     * @return void
     */
    public function notExistsItems()
    {
        if (count($this->orderItems) == 0) {
            $this->addError('order_items', Yii::t('order', 'You need to fill in the list of goods.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'mobile_backend_id' => Yii::t('order', 'Mobile backend id'),
            'mobile_backend_callback_url' => Yii::t('order', 'Mobile backend callback url'),
            'client_phone' => Yii::t('order', 'Client phone'),
            'client_name' => Yii::t('order', 'Client name'),
            'country_id' => Yii::t('order', 'Country'),
            'address' => Yii::t('order', 'Address'),
            'latitude' => Yii::t('order', 'Latitude'),
            'longitude' => Yii::t('order', 'Longitude'),
            'entrance' => Yii::t('order', 'Entrance'),
            'floor' => Yii::t('order', 'Floor'),
            'flat' => Yii::t('order', 'Flat'),
            'order_price' => Yii::t('order', 'Order price'),
            'payment_cash' => Yii::t('order', 'Payment cash'),
            'payment_bonuses' => Yii::t('order', 'Payment bonuses'),
            'payment_status' => Yii::t('order', 'Payment status'),
            'comment_for_operator' => Yii::t('order', 'Comment for operator'),
            'operator_deadline_date' => Yii::t('order', 'Operator deadline date'),
            'operator_real_date' => Yii::t('order', 'Operator real date'),
            'user_id_operator' => Yii::t('order', 'Operator'),
            'comment_for_shop_manager' => Yii::t('order', 'Comment for shop manager'),
            'shop_manager_deadline_date' => Yii::t('order', 'Shop manager deadline date'),
            'shop_manager_real_date' => Yii::t('order', 'Shop manager real date'),
            'user_id_shop_manager' => Yii::t('order', 'Shop manager'),
            'comment_for_driver' => Yii::t('order', 'Comment for driver'),
            'driver_deadline_date' => Yii::t('order', 'Driver deadline date'),
            'driver_real_date' => Yii::t('order', 'Driver real date'),
            'user_id_driver' => Yii::t('order', 'Driver'),
            'shop_id' => Yii::t('order', 'Shop'),
            'source_id' => Yii::t('order', 'Source'),
            'delivery_type' => Yii::t('order', 'Delivery type'),
            'delivery_date' => Yii::t('order', 'Delivery date'),
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
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * Gets query for [[DriverUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDriverUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id_driver']);
    }

    /**
     * Gets query for [[OperatorUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperatorUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id_operator']);
    }

    /**
     * Gets query for [[OrderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::className(), ['order_id' => 'id']);
    }

    /**
     * Gets query for [[OrderDeliveryInYandex]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderDeliveryInYandex()
    {
        return $this->hasMany(OrderDeliveryInYandex::className(), ['order_id' => 'id']);
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
     * Gets query for [[Source]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(Source::className(), ['id' => 'source_id']);
    }

    /**
     * Gets query for [[ShopManagerUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShopManagerUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id_shop_manager']);
    }

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW => Yii::t('order', 'New'),
            self::STATUS_IN_SHOP => Yii::t('order', 'In shop'),
            self::STATUS_AT_DRIVER => Yii::t('order', 'At driver'),
            self::STATUS_DELIVERED => Yii::t('order', 'Delivered'),
            self::STATUS_CANCELED => Yii::t('order', 'Canceled'),
            self::STATUS_GIVEN_TO_KDK_DELIVERY => Yii::t('order', 'Given to KDK delivery'),
        ];
    }

    /**
     * @return array
     */
    public static function getPaymentStatuses(): array
    {
        return [
            self::PAYMENT_STATUS_PAID => Yii::t('order', 'Paid'),
            self::PAYMENT_STATUS_NOT_PAID => Yii::t('order', 'Not paid'),
        ];
    }

    /**
     * @return array
     */
    public static function getTypesDelivery(): array
    {
        return [
            self::TYPE_DELIVERY_SOON_AS_POSSIBLE => Yii::t('order', 'Soon as possible'),
            self::TYPE_DELIVERY_DATE => Yii::t('order', 'Date'),
        ];
    }

    /**
     * @return void
     */
    public function getInformationAboutDeadline(): void
    {
        $query = Order::find()
            ->select(new Expression('
                IF(operator_real_date IS NULL, 0, (IF(TIMEDIFF(operator_real_date, operator_real_date) > 0, 1, 2))) as `status_time_operator`,
                TIMEDIFF(FROM_UNIXTIME(operator_real_date), FROM_UNIXTIME(created_at)) as `factually_time_spent_by_operator`,
                (CASE WHEN shop_manager_deadline_date IS NOT NULL AND shop_manager_real_date IS NULL THEN 0 WHEN shop_manager_deadline_date IS NULL THEN -1 ELSE (IF(TIMEDIFF(shop_manager_deadline_date, shop_manager_real_date) > 0, 1, 2)) END) as `status_time_shop`,
                TIMEDIFF(FROM_UNIXTIME(shop_manager_real_date), FROM_UNIXTIME(operator_real_date)) as `factually_time_spent_by_shop`,
                (CASE WHEN driver_deadline_date IS NOT NULL AND driver_real_date IS NULL THEN 0 WHEN driver_deadline_date IS NULL THEN -1 ELSE (IF(TIMEDIFF(driver_deadline_date, driver_real_date) > 0, 1, 2)) END) as `status_time_driver`,
                TIMEDIFF(FROM_UNIXTIME(driver_real_date), FROM_UNIXTIME(shop_manager_real_date)) as `factually_time_spent_by_driver`,
                TIMEDIFF(FROM_UNIXTIME(driver_real_date), FROM_UNIXTIME(created_at)) as `total_time_spent`,
                IF(TIMEDIFF(NOW(), FROM_UNIXTIME(delivery_date)) > 0, 1, 2) as `has_order_date_passed`
            '))
            ->where(['id' => $this->id])
            ->asArray();

        $this->informationAboutDeadline = $query->one();
    }
}