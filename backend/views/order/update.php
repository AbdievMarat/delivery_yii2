<?php

/* @var $this yii\web\View */
/* @var $modelOrder backend\models\Order */
/* @var $modelsOrderItem backend\models\OrderItem */

$this->title = Yii::t('order', 'Update Order: {name}', [
    'name' => $modelOrder->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $modelOrder->id, 'url' => ['view', 'id' => $modelOrder->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="order-update">

    <div id="order_statuses"></div>

    <?= $this->render('_form', [
        'modelOrder' => $modelOrder,
        'modelsOrderItem' => $modelsOrderItem
    ]) ?>

</div>
