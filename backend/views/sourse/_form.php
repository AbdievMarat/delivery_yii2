<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Sourse */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sourse-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(\backend\models\Sourse::getStatuses()) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('sourse', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
