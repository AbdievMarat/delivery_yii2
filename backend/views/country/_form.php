<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Country */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="country-form">
    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'name_currency')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'name_organization')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'contact_phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'token_yandex')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'token_mobile_backend')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'status')->dropDownList(\backend\models\Country::getStatuses()) ?>
        </div>
        <div class="col-md-6">
        </div>
    </div>

    <div class="col-md-12 form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-group-justified btn-success' : 'btn btn-group-justified btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
