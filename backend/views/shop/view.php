<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Shop */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('shop', 'Shops'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="shop-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (Yii::$app->user->can('shop.update')) { ?>
            <?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php } ?>

        <?php if (Yii::$app->user->can('shop.delete')) { ?>
            <?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php } ?>
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
                    return ArrayHelper::getValue($model->getStatuses(), $model->status, Yii::t('backend', 'Undefined'));
                },
            ],
        ],
    ]) ?>

</div>
