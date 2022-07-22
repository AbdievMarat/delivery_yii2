<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Shop */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('shop', 'Shops'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="shop-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('shop', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('shop', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('shop', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'country_id',
                'value' => function ($model) {
                    if (isset($model->country)) {
                        return $model->country->name;
                    } else {
                        return Yii::t('backend', 'Undefined');
                    }
                },
            ],
            'name',
            'contact_phone',
            'address',
            'latitude',
            'longitude',
            'mobile_backend_id',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    if (isset($model->getStatuses()[$model->status])) {
                        return $model->getStatuses()[$model->status];
                    } else {
                        return Yii::t('backend', 'Undefined');
                    }
                },
            ],
        ],
    ]) ?>

</div>
