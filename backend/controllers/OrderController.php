<?php

namespace backend\controllers;

use backend\models\Country;
use backend\models\Model;
use backend\models\Order;
use backend\models\OrderDeliveryInYandex;
use backend\models\OrderItem;
use backend\models\OrderSearch;
use backend\traits\DeliveryYandexTrait;
use SoapClient;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    use DeliveryYandexTrait;
    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', 'view'],
                            'roles' => ['admin', 'manager', 'operator'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create', 'update', 'delete', 'product-search', 'get-country-shops', 'get-remains-products', 'get-orders-delivery-in-yandex', 'create-order-yandex', 'accept-order-yandex', 'cancel-order-yandex-info', 'cancel-order-yandex', 'transfer-order-to-shop', 'transfer-order-to-driver', 'order-delivered'],
                            'roles' => ['admin'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Order models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param int $id Код
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $modelOrder = new Order();
        $modelsOrderItem = [new OrderItem];

        if ($this->request->isPost) {
            $data = $this->request->post();

            $modelsOrderItem = Model::createMultiple(OrderItem::classname());

            // validate all models
            $validOrder = $modelOrder->load($data) && $modelOrder->validate();
            $validOrderItem = Model::loadMultiple($modelsOrderItem, $data) && Model::validateMultiple($modelsOrderItem);

            if ($validOrder && $validOrderItem) {
                $modelOrder->operator_deadline_date = strtotime("+".Order::OPERATOR_DEADLINE_IN_MINUTES." minute", time());

                if ($flag = $modelOrder->save()) {
                    foreach ($modelsOrderItem as $modelOrderItem) {
                        $modelOrderItem->order_id = $modelOrder->id;
                        $modelOrderItem->save();
                    }
                }
                if ($flag) {
                    return $this->redirect(['view', 'id' => $modelOrder->id]);
                }
            }
        } else {
            $modelOrder->loadDefaultValues();
        }

        return $this->render('create', [
            'modelOrder' => $modelOrder,
            'modelsOrderItem' => (empty($modelsOrderItem)) ? [new OrderItem] : $modelsOrderItem
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id Код
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id)
    {
        $modelOrder = $this->findModel($id);
        $modelsOrderItem = $modelOrder->orderItems;

        $data = $this->request->post();

        if ($this->request->isPost && $modelOrder->load($data) && $modelOrder->save()) {
            $oldIDs = ArrayHelper::map($modelsOrderItem, 'id', 'id');
            $modelsOrderItem = Model::createMultiple(OrderItem::classname(), $modelsOrderItem);
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsOrderItem, 'id', 'id')));

            // validate all models
            $validOrder = $modelOrder->validate();
            $validOrderItem = Model::loadMultiple($modelsOrderItem, $data) && Model::validateMultiple($modelsOrderItem);

            if ($validOrder && $validOrderItem) {
                if ($flag = $modelOrder->save()) {
                    if (! empty($deletedIDs)) {
                        OrderItem::deleteAll(['id' => $deletedIDs]);
                    }
                    foreach ($modelsOrderItem as $modelOrderItem) {
                        $modelOrderItem->order_id = $modelOrder->id;
                        $modelOrderItem->save();
                    }
                }
                if ($flag) {
                    return $this->redirect(['view', 'id' => $modelOrder->id]);
                }
            }
        }

        return $this->render('update', [
            'modelOrder' => $modelOrder,
            'modelsOrderItem' => (empty($modelsOrderItem)) ? [new OrderItem] : $modelsOrderItem
        ]);
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id Код
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id Код
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Order
    {
        if (($model = Order::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'The requested page does not exist.'));
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionProductSearch(): string
    {
        $country_id = $this->request->get('country_id');
        $desired_product = $this->request->get('desired_product');

        $token_mobile_backend = Country::find()
            ->select('token_mobile_backend')
            ->where(['id' => $country_id])
            ->scalar();
        $password_curl = '';

        $client = new Client();
        $response = $client->createRequest()
            ->addHeaders(["content-type" => "application/x-www-form-urlencoded", "Authorization" => "Basic ". base64_encode("$token_mobile_backend:$password_curl")])
            ->setMethod('GET')
            ->setUrl('https://api.kulikov.com/v2/partner/products/product-search')
            ->setData(['product' => $desired_product])
            ->send();

        if ($response->isOk) {
            return Json::encode($response->data);
        }

        return Json::encode(['success' => false]);
    }

    /**
     * shops received by country_id
     * @return string
     */
    public function actionGetCountryShops(): string
    {
        $country_id = $this->request->get('country_id');

        $country = Country::findOne($country_id);
        if($country){
            $shops = $country->getShops()->all();

            if ($shops) {
                return Json::encode(['success' => true, 'shops' => $shops, 'country_latitude' => $country->latitude, 'country_longitude' => $country->longitude]);
            }
        }

        return Json::encode(['success' => false]);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionGetRemainsProducts(): string
    {
        $country_id = $this->request->get('country_id');
        $products = $this->request->get('products');
        $shop_mobile_backend_id = $this->request->get('shop_mobile_backend_id');

        $success = false;
        $date_withdrawal_remains = '';

        if($country_id == 2){
            foreach ($products as $key => $value){
                $wsdl = 'http://192.168.111.3/Roznica2/ws/Ostatki?wsdl';
                $options = array(
                    'login' => 'root',
                    'password' => '123',
                );

                $params = array(
                    "sku" => $value['product_code'], //"Р-000005733"
                    "Tovar" => "",
                    "Kol" => "",
                    "ID" => $shop_mobile_backend_id //"1024"
                );

                try {
                    $client = new SoapClient($wsdl, $options);
                    $return = $client->__soapCall("Start", array($params))->return;
                    $data = json_decode($return)[0];
                    $date_withdrawal_remains = $data->Date;
                    $success = true;
                } catch (\SoapFault $e) {
                    $data = [];
                }

                if($data)
                    $products[$key]['remainder'] = $data->Kol;
            }
        }
        else if($country_id == 3){
            $username_curl = 'MobileExchangeAPI';
            $password_curl = '564236';
            $client = new Client();
            $response = $client->createRequest()
                ->addHeaders(["content-type" => "application/x-www-form-urlencoded"])
                ->setOptions([
                    CURLOPT_USERPWD => "$username_curl:$password_curl",
                ])
                ->setMethod('GET')
                ->setUrl('http://10.5.15.10/product/hs/remains/ЦБ-00070021/1019')
                ->send();
        }
        if ($response->isOk) {
            return Json::encode($response->data);
        }

        return Json::encode(['success' => $success, 'products' => $products, 'date_withdrawal_remains' => $date_withdrawal_remains]);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionGetOrdersDeliveryInYandex(): string
    {
        $order_id = $this->request->get('order_id');

        $token_yandex = $this->getTokenYandex($order_id);

        $orders_in_yandex = OrderDeliveryInYandex::find()
            ->select("*")//, FROM_UNIXTIME(created_at, '%d-%m-%Y %H:%i') as `created_date` без учета часового пояса
            ->where(['order_id' => $order_id])
            ->with([
                'user' => function ($query) {
                    $query->select('id, username');
                },
            ])
            ->asArray()
            ->all();

        foreach($orders_in_yandex as $key => $order){
            $status = $order['status'];

            //если заявки не завершенные
            if($status != 'cancelled' && $status != 'delivered'){
                $response_yandex = $this->getOrderYandexInfo($token_yandex, $order['yandex_id']);
                if($response_yandex->statusCode == 200){
                    $status = $response_yandex->data['status'];
                    $offer_price = $response_yandex->data['pricing']['offer']['price'];

                    $modelOrderDeliveryInYandex = OrderDeliveryInYandex::findOne($order['id']);
                    $modelOrderDeliveryInYandex->status = $status;
                    $modelOrderDeliveryInYandex->offer_price = $offer_price;
                    $modelOrderDeliveryInYandex->save();

                    $orders_in_yandex[$key]['status'] = $status;
                    $orders_in_yandex[$key]['offer_price'] = $offer_price;
                }
            }

            $orders_in_yandex[$key]['status_description'] = ArrayHelper::getValue(OrderDeliveryInYandex::getYandexStatuses(), $status, Yii::t('backend', 'Undefined'));
            $orders_in_yandex[$key]['created_at'] = date('d.m.Y h:i', $order['created_at']);
        }

        $modelOrder = Order::findOne($order_id);
        $modelOrder->getOperatorUser();
        $modelOrder->getShop();
        $modelOrder->getInformationAboutDeadline();

        $order_statuses_view = $this->renderPartial('_statuses', [
            'modelOrder' => $modelOrder
        ]);

        return Json::encode(['success' => true, 'orders_delivery_in_yandex' => $orders_in_yandex, 'order_statuses_view' => $order_statuses_view]);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionCreateOrderYandex(): string
    {
        $errors = [];
        $success = false;
        $count_of_deliveries = 0;

        $data = $this->request->get();

        $modelOrder = $this->findModel($data['order_id']);
        $modelOrder->shop_id = $data['shop_id'];
        $modelOrder->client_phone = $data['client_phone'];
        $modelOrder->address = $data['address'];
        $modelOrder->latitude = $data['latitude'];
        $modelOrder->longitude = $data['longitude'];
        $modelOrder->entrance = $data['entrance'];
        $modelOrder->floor = $data['floor'];
        $modelOrder->flat = $data['flat'];
        $modelOrder->comment_for_operator = $data['comment_for_operator'];
        $modelOrder->comment_for_shop_manager = $data['comment_for_shop_manager'];
        $modelOrder->comment_for_driver = $data['comment_for_driver'];

        $modelOrder->scenario = 'delivery';

        if($modelOrder->validate() && $modelOrder->save()){
            $order_items = $modelOrder->orderItems;
            $order_country = $modelOrder->country;
            $order_shop = $modelOrder->shop;
            $token_yandex = $this->getTokenYandex($data['order_id']);

            /** @var OrderItem $items */
            foreach ($order_items as $item) {
                $items[] = array(
                    'cost_currency' => $order_country->currency_iso,
                    'cost_value' => '0',
                    'droppof_point' => 1,// Идентификатор точки, куда нужно доставить товар. Должен соответствовать значению route_points[].point_id
                    'pickup_point' => 2,// Идентификатор точки, откуда нужно забрать товар. Должен соответствовать значению route_points[].point_id
                    'quantity' => $item['amount'],
                    'size' => array(
                        'height' => 0.002,
                        'length' => 0.002,
                        'width' => 0.002,
                    ),
                    'title' => $item['product_name'],
                    'weight' => 0.002,
                );
            }

            $yandex_order_data['emergency_contact'] = array(
                'name' => $order_country->name_organization,
                'phone' => $order_country->contact_phone
            );
            $yandex_order_data['items'] = $items;
            $yandex_order_data['route_points'] = array(
                array(
                    'address' => array(
                        'comment' => 'Доставка из магазина Куликовский <'.$order_shop->name.'>. Сообщите менеджеру, что заказ по доставке Яндекс.Такси. Назовите номер заказа <'.$modelOrder->id.'> и заберите продукцию.',
                        'coordinates' => [floatval($modelOrder->longitude), floatval($modelOrder->latitude)],
                        'fullname' => $modelOrder->address
                    ),
                    'contact' => array(
                        'name' => $order_country->name_organization,
                        'phone' => $order_country->contact_phone
                    ),
                    'point_id' => 2,// Идентификатор точки. Должен соответствовать значению c pickup_point
                    'type' => 'source',// Точка отправления, где курьер забирает товар
                    'visit_order' => 1,// Порядок посещения точки (нумерация с 1)
                    'skip_confirmation' => true// Пропускать подтверждение через SMS в данной точке
                ),
                array(
                    'address' => array(
                        'comment' => 'Заказ оплачен безналично, при передаче заказа нельзя требовать с получателя деньги за доставку. '.$modelOrder->comment_for_driver,
                        'coordinates' => [floatval($order_shop->longitude), floatval($order_shop->latitude)],
                        'fullname' => $order_shop->name,
                        'porch' => $modelOrder->entrance,// Подъезд
                        'sfloor' => $modelOrder->floor,// Этаж
                        'sflat' => $modelOrder->flat// Квартира
                    ),
                    'contact' => array(
                        'name' => $modelOrder->client_name,
                        'phone' => '+'.$modelOrder->client_phone
                    ),
                    'point_id' => 1,// Идентификатор точки. Должен соответствовать значению c droppof_point
                    'type' => 'destination',// Точка назначения, где курьер передает товар
                    'visit_order' => 2,// // Порядок посещения точки (нумерация с 1)
                    'skip_confirmation' => true// Пропускать подтверждение через SMS в данной точке
                )
            );
            $yandex_order_data['client_requirements'] = array(
                'cargo_options' => ['auto_courier']// Курьер только на машине
            );

            //отправляет заказ во все выбранные тарифы в справочнике стран
            foreach (explode(',', $order_country->yandex_tariffs) as $yandex_tariff){
                $yandex_order_data['client_requirements']['taxi_class'] = $yandex_tariff;

                $response_yandex = $this->createOrderYandex($token_yandex, $yandex_order_data);

                if($response_yandex->statusCode == 400){
                    $errors['order_yandex'] = [$response_yandex->data['message']];
                }
                else if($response_yandex->statusCode == 200){
                    $success = true;

                    $modelOrderDeliveryInYandex = new OrderDeliveryInYandex();
                    $modelOrderDeliveryInYandex->order_id = $modelOrder->id;
                    $modelOrderDeliveryInYandex->yandex_id = $response_yandex->data['id'];
                    $modelOrderDeliveryInYandex->shop_id = $modelOrder->shop_id;
                    $modelOrderDeliveryInYandex->shop_address = $order_shop->address;
                    $modelOrderDeliveryInYandex->shop_latitude = $order_shop->latitude;
                    $modelOrderDeliveryInYandex->shop_longitude = $order_shop->longitude;
                    $modelOrderDeliveryInYandex->client_address = $modelOrder->address;
                    $modelOrderDeliveryInYandex->client_latitude = $modelOrder->latitude;
                    $modelOrderDeliveryInYandex->client_longitude = $modelOrder->longitude;
                    $modelOrderDeliveryInYandex->tariff = $response_yandex->data['client_requirements']['taxi_class'];
                    $modelOrderDeliveryInYandex->status = $response_yandex->data['status'];
                    $modelOrderDeliveryInYandex->user_id = Yii::$app->user->id;
                    $modelOrderDeliveryInYandex->save();
                }
            }

            if($success)
                $count_of_deliveries = $modelOrder->getOrderDeliveryInYandex()->count();
        }
        else{
            $errors = array_merge($errors, $modelOrder->getErrors());
        }

        return Json::encode(['success' => $success, 'errors' => $errors, 'count_of_deliveries' => $count_of_deliveries]);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionAcceptOrderYandex(): string
    {
        $order_id = $this->request->get('order_id');
        $yandex_id = $this->request->get('yandex_id');

        $success = false;

        $token_yandex = $this->getTokenYandex($order_id);

        $response_yandex = $this->acceptOrderYandex($token_yandex, $yandex_id);

        if (!empty($response_yandex)) {
            if($response_yandex->statusCode == 200 && $response_yandex->data['status'] == 'accepted')
                $success = true;
        }

        return Json::encode(['success' => $success]);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionCancelOrderYandexInfo(): string
    {
        $order_id = $this->request->get('order_id');
        $yandex_id = $this->request->get('yandex_id');

        $success = false;

        $token_yandex = $this->getTokenYandex($order_id);

        $response_yandex = $this->cancelOrderYandexInfo($token_yandex, $yandex_id);

        if (!empty($response_yandex)) {
            if($response_yandex->statusCode == 200){
                $success = true;

                $cancel_state = $response_yandex->data['cancel_state'];
            }
        }

        return Json::encode(['success' => $success, 'cancel_state' => $cancel_state]);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionCancelOrderYandex(): string
    {
        $order_id = $this->request->get('order_id');
        $yandex_id = $this->request->get('yandex_id');
        $cancel_state = $this->request->get('cancel_state', 'free');

        $success = false;

        $token_yandex = $this->getTokenYandex($order_id);

        $response_yandex = $this->cancelOrderYandex($token_yandex, $yandex_id, $cancel_state);

        if (!empty($response_yandex)) {
            if($response_yandex->statusCode == 200 && $response_yandex->data['status'] == 'cancelled')
                $success = true;
        }

        return Json::encode(['success' => $success]);
    }

    public function actionTransferOrderToShop()
    {
        $order_id = $this->request->get('order_id');
        $shop_id = $this->request->get('shop_id');

        $success = false;

        $modelOrder = $this->findModel($order_id);
        $modelOrder->shop_id = $shop_id;
        $modelOrder->status = Order::STATUS_IN_SHOP;
        $modelOrder->operator_real_date = time();
        $modelOrder->shop_manager_deadline_date = strtotime("+".Order::SHOP_DEADLINE_IN_MINUTES." minute", time());
        $modelOrder->user_id_operator = Yii::$app->user->id;

        if($modelOrder->validate() && $modelOrder->save()){
            $success = true;
        }

        return Json::encode(['success' => $success]);
    }

    public function actionTransferOrderToDriver()
    {
        $order_id = $this->request->get('order_id');

        $success = false;

        $modelOrder = $this->findModel($order_id);
        $modelOrder->status = Order::STATUS_AT_DRIVER;
        $modelOrder->shop_manager_real_date = time();
        $modelOrder->driver_deadline_date = strtotime("+".Order::DRIVER_DEADLINE_IN_MINUTES." minute", time());
        $modelOrder->user_id_shop_manager = Yii::$app->user->id;

        if($modelOrder->validate() && $modelOrder->save()){
            $success = true;
        }

        return Json::encode(['success' => $success]);
    }

    public function actionOrderDelivered()
    {
        $order_id = $this->request->get('order_id');

        $success = false;

        $modelOrder = $this->findModel($order_id);
        $modelOrder->status = Order::STATUS_DELIVERED;
        $modelOrder->driver_real_date = time();

        if($modelOrder->validate() && $modelOrder->save()){
            $success = true;
        }

        return Json::encode(['success' => $success]);
    }
}