<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'mobile_backend_id',
            'mobile_backend_callback_url:url',
            'client_id',
            'country_id',
            'address',
            'latitude',
            'longitude',
            'entrance',
            'floor',
            'flat',
            'order_price',
            'payment_cash',
            'payment_bonuses',
            'payment_status',
            'comment_for_operator',
            'operator_deadline_date',
            'operator_real_date',
            'user_id_operator',
            'comment_for_shop_manager',
            'shop_manager_deadline_date',
            'shop_manager_real_date',
            'user_id_shop_manager',
            'comment_for_driver',
            'driver_deadline_date',
            'driver_real_date',
            'user_id_driver',
            'shop_id',
            'delivery_type',
            'delivery_date',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
