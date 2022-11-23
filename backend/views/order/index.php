<?php

use backend\models\Country;
use backend\models\Order;
use backend\models\Source;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('order', 'Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('order', 'Create Order'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'mobile_backend_id',
            [
                'attribute' => 'created_at',
                'format' => 'html',
                'value' => function ($model) {
                    return date('d.m.Y H:i', $model->created_at);
                },
            ],
            [
                'attribute' => 'delivery_type',
                'format' => 'html',
                'value' => function ($model) {
                    return ArrayHelper::getValue($model->getTypesDelivery(), $model->delivery_type, Yii::t('backend', 'Undefined'));
                },
                'filter' => Order::getTypesDelivery(),
            ],
            [
                'attribute' => 'source_id',
                'format' => 'html',
                'value' => function ($model) {
                    return isset($model->source) ? $model->source->name : Yii::t('backend', 'Undefined');
                },
                'filter' => Source::getSourcesList(),
            ],
            [
                'attribute' => 'country_id',
                'format' => 'html',
                'value' => function ($model) {
                    return isset($model->country) ? $model->country->name : Yii::t('backend', 'Undefined');
                },
                'filter' => Country::getCountriesList(),
            ],
            //'client_id',
            //'address',
            //'latitude',
            //'longitude',
            //'entrance',
            //'floor',
            //'flat',
            //'order_price',
            //'payment_cash',
            //'payment_bonuses',
            //'payment_status',
            //'comment_for_operator',
            //'operator_deadline_date',
            //'operator_real_date',
            //'user_id_operator',
            //'comment_for_shop_manager',
            //'shop_manager_deadline_date',
            //'shop_manager_real_date',
            //'user_id_shop_manager',
            //'comment_for_driver',
            //'driver_deadline_date',
            //'driver_real_date',
            //'user_id_driver',
            //'shop_id',
            //'delivery_date',
            //'status',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Order $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
