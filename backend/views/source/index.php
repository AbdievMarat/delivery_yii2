<?php

use backend\models\Source;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SourceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('source', 'Sources');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="source-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('source.create')) { ?>
        <p>
            <?= Html::a(Yii::t('source', 'Create Source'), ['create'], ['class' => 'btn btn-success']) ?>
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
            'name',
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function ($model) {
                    return ArrayHelper::getValue($model->getStatuses(), $model->status, Yii::t('backend', 'Undefined'));
                },
                'filter' => Source::getStatuses(),
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {update} {delete}',
                'headerOptions' => ['width' => '80'],
                'buttons' => [
                    'view' => function ($url) {
                        if (Yii::$app->user->can('source.view')) {
                            return Html::a('<i class="bi bi-eye-fill"></i>', $url, ['title' => Yii::t('backend', 'View')]);
                        } else {
                            return '';
                        }
                    },
                    'update' => function ($url) {
                        if (Yii::$app->user->can('source.update')) {
                            return Html::a('<i class="bi bi-pencil-fill"></i>', $url, ['title' => Yii::t('backend', 'Update')]);
                        } else {
                            return '';
                        }
                    },
                    'delete' => function ($url) {
                        if (Yii::$app->user->can('source.delete')) {
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
