<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Country */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('country', 'Countries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="country-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (Yii::$app->user->can('country.update')) { ?>
            <?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php } ?>

        <?php if (Yii::$app->user->can('country.delete')) { ?>
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
            'name',
            'name_currency',
            'name_organization',
            'contact_phone',
            'token_yandex',
            'token_mobile_backend',
            'address',
            'latitude',
            'longitude',
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
            [
                'attribute' => 'yandexTariffs',
                'value' => function ($model) {
                    if ($model->getYandexTariffs() !== null) {
                        return implode(', ', array_column($model->getYandexTariffs()->select('name_tariff')->asArray()->all(), 'name_tariff'));
                    } else {
                        return Yii::t('backend', 'Undefined');
                    }
                },
            ],
        ],
    ]) ?>

</div>
