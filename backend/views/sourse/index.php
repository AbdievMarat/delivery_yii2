<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\Sourse;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\SourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('sourse', 'Sourses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sourse-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <h1><?= Yii::$app->formatter->asDate('2022-04-22', 'long') ?></h1>
    <p>
        <?= Html::a(Yii::t('sourse', 'Create Sourse'), ['create'], ['class' => 'btn btn-success']) ?>
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
            'availability',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Sourse $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
