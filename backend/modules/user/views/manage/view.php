<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (Yii::$app->user->can('user.manage.update')) { ?>
            <?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php } ?>

        <?php if (Yii::$app->user->can('user.manage.delete')) { ?>
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
            'avatarImage:image',
            'id',
            'username',
            'email:email',
            [
                'attribute' => 'role',
                'value' => function ($model) {
                    return implode(',', $model->getRole());
                },
            ],
            [
                'attribute' => 'available_countries',
                'value' => function ($model) {
                    return implode(',', $model->getAvailableCountriesList());
                },
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return ArrayHelper::getValue($model->getStatuses(), $model->status, Yii::t('backend', 'Undefined'));
                },
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
