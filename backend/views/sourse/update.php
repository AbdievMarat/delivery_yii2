<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Sourse */

$this->title = Yii::t('sourse', 'Update Sourse: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('sourse', 'Sourses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('sourse', 'Update');
?>
<div class="sourse-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
