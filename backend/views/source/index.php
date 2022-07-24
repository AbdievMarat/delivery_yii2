<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\Source;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SourceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('source', 'Sources');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="source-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('source', 'Create Source'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
                    if (isset($model->getStatuses()[$model->status])) {
                        return $model->getStatuses()[$model->status];
                    } else {
                        return Yii::t('backend', 'Undefined');
                    }
                },
                'filter' => \backend\models\Source::getStatuses(),
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Source $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
