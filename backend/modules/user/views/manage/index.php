<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('user', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('user.manage.create')) { ?>
        <p>
            <?= Html::a(Yii::t('user', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php } ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'avatarImage:image',
            'id',
            'username',
            'email:email',
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function ($model) {
                    if (isset($model->getStatuses()[$model->status])) {
                        return $model->getStatuses()[$model->status];
                    } else {
                        return Yii::t('backend', 'Undefined');
                    }
                },
                'filter' => User::getStatuses(),
            ],
            'created_at:datetime',
            [
                'attribute' => 'role',
                'value' => function ($model) {
                    return implode(',', $model->getRole());
                },
            ],

            [
                'class' => ActionColumn::className(),
                'template' => '{view} {update} {delete}',
                'headerOptions' => ['width' => '80'],
                'buttons' => [
                    'view' => function ($url) {
                        if (Yii::$app->user->can('user.manage.view')) {
                            return Html::a('<i class="bi bi-eye-fill"></i>', $url, ['title' => Yii::t('backend', 'View')]);
                        } else {
                            return '';
                        }
                    },
                    'update' => function ($url) {
                        if (Yii::$app->user->can('user.manage.update')) {
                            return Html::a('<i class="bi bi-pencil-fill"></i>', $url, ['title' => Yii::t('backend', 'Update')]);
                        } else {
                            return '';
                        }
                    },
                    'delete' => function ($url) {
                        if (Yii::$app->user->can('user.manage.delete')) {
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


</div>
