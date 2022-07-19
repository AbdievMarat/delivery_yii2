<?php

namespace backend\widgets;

use Yii;
use yii\bootstrap\Widget;

class YandexMap extends Widget
{
    /***
     * @var string
     * Yandex API key https://developer.tech.yandex.ru/services/
     */
    public $keyAPI = 'bb7a3c07-0e20-4b26-a12f-6759b702d5e3';

    /**
     * @var string
     * Input id for Address
     */
    public $formIdAddress = '';

    /**
     * @var string
     * Input id for latitude
     */
    public $formIdLatitude = '';

    /**
     * @var string
     * Input id for longitude
     */
    public $formIdLonitude = '';

    /***
     * @var float
     * Default latitude
     */
    public $centerLatitude = 42.87256;

    /***
     * @var float
     * Default longitude
     */
    public $centerLonitude = 74.59554;

    /***
     * @var int
     * Default zoom
     */
    public $zoom = 15;

    /***
     * @var string
     * Yandex maps language
     */
    public $lang = 'ru_RU';

    /***
     * @var array
     * Place markers
     * lat => latitude
     * lon => longitude
     * name => name
     * content => bullon content
     */
    public $placeMark = [];

    /**
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return string|void
     */
    public function run()
    {
        $params = [
            'keyAPI' => Yii::$app->params['yandexMaps']['keyAPI'] ?? $this->keyAPI,
            'centerLatitude' => Yii::$app->params['yandexMaps']['centerLatitude'] ?? $this->centerLatitude,
            'centerLonitude' => Yii::$app->params['yandexMaps']['centerLonitude'] ?? $this->centerLonitude,
            'zoom' => Yii::$app->params['yandexMaps']['zoom'] ?? $this->zoom,
            'lang' => Yii::$app->params['yandexMaps']['lang'] ?? $this->lang,
            'formIdAddress' => $this->formIdAddress,
            'formIdLatitude' => $this->formIdLatitude,
            'formIdLonitude' => $this->formIdLonitude,
            'placeMark' => $this->placeMark,
        ];

        return $this->render('yandex_map', $params);
    }
}