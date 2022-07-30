<?php

use backend\models\Country;
use backend\models\Shop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ShopSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('shop', 'Shops');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('shop.create')) { ?>
        <p>
            <?= Html::a(Yii::t('shop', 'Create Shop'), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php } ?>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

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
                'filter' => Country::getCountriesList(),
            ],
            'name',
            'contact_phone',
            'address',
            //'latitude',
            //'longitude',
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
                'filter' => Shop::getStatuses(),
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {update} {delete}',
                'headerOptions' => ['width' => '80'],
                'buttons' => [
                    'view' => function ($url) {
                        if (Yii::$app->user->can('shop.view')) {
                            return Html::a('<i class="bi bi-eye-fill"></i>', $url, ['title' => Yii::t('backend', 'View')]);
                        } else {
                            return '';
                        }
                    },
                    'update' => function ($url) {
                        if (Yii::$app->user->can('shop.update')) {
                            return Html::a('<i class="bi bi-pencil-fill"></i>', $url, ['title' => Yii::t('backend', 'Update')]);
                        } else {
                            return '';
                        }
                    },
                    'delete' => function ($url) {
                        if (Yii::$app->user->can('shop.delete')) {
                            return Html::a('<i class="bi bi-trash-fill"></i>', $url, [
                                'title' => Yii::t('backend', 'Delete'),
                                'data' => [
                                    'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ]);
                        } else {
                            return '';
                        }
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
