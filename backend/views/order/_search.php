<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'mobile_backend_id') ?>

    <?= $form->field($model, 'mobile_backend_callback_url') ?>

    <?= $form->field($model, 'client_id') ?>

    <?= $form->field($model, 'country_id') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'latitude') ?>

    <?php // echo $form->field($model, 'longitude') ?>

    <?php // echo $form->field($model, 'entrance') ?>

    <?php // echo $form->field($model, 'floor') ?>

    <?php // echo $form->field($model, 'flat') ?>

    <?php // echo $form->field($model, 'order_price') ?>

    <?php // echo $form->field($model, 'payment_cash') ?>

    <?php // echo $form->field($model, 'payment_bonuses') ?>

    <?php // echo $form->field($model, 'payment_status') ?>

    <?php // echo $form->field($model, 'comment_for_operator') ?>

    <?php // echo $form->field($model, 'operator_deadline_date') ?>

    <?php // echo $form->field($model, 'operator_real_date') ?>

    <?php // echo $form->field($model, 'user_id_operator') ?>

    <?php // echo $form->field($model, 'comment_for_shop_manager') ?>

    <?php // echo $form->field($model, 'shop_manager_deadline_date') ?>

    <?php // echo $form->field($model, 'shop_manager_real_date') ?>

    <?php // echo $form->field($model, 'user_id_shop_manager') ?>

    <?php // echo $form->field($model, 'comment_for_driver') ?>

    <?php // echo $form->field($model, 'driver_deadline_date') ?>

    <?php // echo $form->field($model, 'driver_real_date') ?>

    <?php // echo $form->field($model, 'user_id_driver') ?>

    <?php // echo $form->field($model, 'shop_id') ?>

    <?php // echo $form->field($model, 'delivery_type') ?>

    <?php // echo $form->field($model, 'delivery_date') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
