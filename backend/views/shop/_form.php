<?php

use backend\models\Country;
use backend\widgets\YandexMap;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Shop */
/* @var $form yii\bootstrap5\ActiveForm */
?>

<div class="shop-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'country_id')->dropDownList(Country::getCountriesList()) ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'contact_phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'mobile_backend_id')->textInput() ?>

            <?= $form->field($model, 'status')->dropDownList(\backend\models\Shop::getStatuses()) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

            <p id="notice"></p>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'latitude')->textInput(['maxlength' => true,'readonly'=> true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'longitude')->textInput(['maxlength' => true,'readonly'=> true]) ?>
                </div>
            </div>

            <?php
            try {
                $placeMark['placeMark'] = [
                    'lat' => $model->latitude,
                    'lon' => $model->longitude,
                    'name' => $model->name,
                    'content' => sprintf(
                        '<h6 class="small">%s<br/><abbr class="address-line full-width" title="%s">%s: </abbr><a href="tel:%s">%s</a></h6>',
                        Html::encode($model->address),
                        Yii::t('backend', 'Phone'),
                        Yii::t('backend', 'Phone'),
                        Html::encode($model->contact_phone),
                        Html::encode($model->contact_phone),
                    ),
                ];
            } catch (\Exception $e) {
                $placeMark = [];
            }

            $defaultsForMap = [
                'formIdAddress' => Html::getInputId($model, 'address'),
                'formIdLatitude' => Html::getInputId($model, 'latitude'),
                'formIdLongitude' => Html::getInputId($model, 'longitude'),
            ];

            echo YandexMap::widget(array_merge($placeMark, $defaultsForMap));
            ?>
        </div>
    </div>

    <div class="mb-3">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success w-100' : 'btn btn-primary w-100']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
