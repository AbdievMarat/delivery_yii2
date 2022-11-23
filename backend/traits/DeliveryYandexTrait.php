<?php

namespace backend\traits;

use backend\models\Order;
use Faker\Provider\Uuid;
use yii\httpclient\Client;

trait DeliveryYandexTrait
{
    /**
     * @param $order_id
     * @return false|int|string|null
     */
    public function getTokenYandex($order_id)
    {
        return Order::find()
            ->select('countries.token_yandex')
            ->where(['orders.id' => $order_id])
            ->leftJoin('countries', 'orders.country_id = countries.id')
            ->scalar();
    }

    /**
     * @param $token_yandex
     * @param $yandex_order_data
     * @return \yii\httpclient\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function createOrderYandex($token_yandex, $yandex_order_data): \yii\httpclient\Response
    {
        $client = new Client();
        return $client->createRequest()
            ->setUrl('https://b2b.taxi.yandex.net/b2b/cargo/integration/v2/claims/create?request_id='.Uuid::uuid())
            ->addHeaders(["Accept-Language" => "ru"])
            ->setMethod('POST')
            ->addHeaders(['Authorization' => 'Bearer ' . $token_yandex])
            ->setContent(json_encode($yandex_order_data, JSON_UNESCAPED_UNICODE))
            ->send();
    }

    /**
     * @param $token_yandex
     * @param $yandex_id
     * @return \yii\httpclient\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function acceptOrderYandex($token_yandex, $yandex_id): \yii\httpclient\Response
    {
        $data['version'] = 1;

        $client = new Client();
        return $client->createRequest()
            ->setUrl('https://b2b.taxi.yandex.net/b2b/cargo/integration/v1/claims/accept?claim_id='.$yandex_id)
            ->addHeaders(["Accept-Language" => "ru"])
            ->setMethod('POST')
            ->setContent(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->addHeaders(['Authorization' => 'Bearer ' . $token_yandex])
            ->send();
    }

    /**
     * @param $token_yandex
     * @param $yandex_id
     * @return \yii\httpclient\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function cancelOrderYandexInfo($token_yandex, $yandex_id): \yii\httpclient\Response
    {
        $client = new Client();
        return $client->createRequest()
            ->setUrl('https://b2b.taxi.yandex.net/b2b/cargo/integration/v2/claims/cancel-info?claim_id='.$yandex_id)
            ->addHeaders(["Accept-Language" => "ru"])
            ->setMethod('POST')
            ->addHeaders(['Authorization' => 'Bearer ' . $token_yandex])
            ->send();
    }

    /**
     * @param $token_yandex
     * @param $yandex_id
     * @param $cancel_state
     * @return \yii\httpclient\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function cancelOrderYandex($token_yandex, $yandex_id, $cancel_state): \yii\httpclient\Response
    {
        $data['version'] = 1;
        $data['cancel_state'] = $cancel_state;

        $client = new Client();
        return $client->createRequest()
            ->setUrl('https://b2b.taxi.yandex.net/b2b/cargo/integration/v1/claims/cancel?claim_id='.$yandex_id)
            ->addHeaders(["Accept-Language" => "ru"])
            ->setMethod('POST')
            ->setContent(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->addHeaders(['Authorization' => 'Bearer ' . $token_yandex])
            ->send();
    }

    public function getDriverPositionYandex()
    {
        // TODO: Implement getDriverPosition() method.
    }

    public function getDriverPhoneYandex()
    {
        // TODO: Implement getDriverPhone() method.
    }

    /**
     * @param $token_yandex
     * @param $yandex_id
     * @return \yii\httpclient\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function getOrderYandexInfo($token_yandex, $yandex_id): \yii\httpclient\Response
    {
        $client = new Client();
        return $client->createRequest()
            ->setUrl('https://b2b.taxi.yandex.net/b2b/cargo/integration/v2/claims/info?claim_id='.$yandex_id)
            ->addHeaders(["Accept-Language" => "ru"])
            ->setMethod('POST')
            ->addHeaders(['Authorization' => 'Bearer ' . $token_yandex])
            ->send();
    }
}