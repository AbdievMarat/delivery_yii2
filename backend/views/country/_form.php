<?php

use backend\widgets\YandexMap;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use backend\models\OrderDeliveryInYandex;

/* @var $this yii\web\View */
/* @var $model backend\models\Country */
/* @var $form yii\bootstrap5\ActiveForm */
?>

<div class="country-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'name_currency')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'currency_iso')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'name_organization')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'contact_phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'token_yandex')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'token_mobile_backend')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'status')->dropDownList($model->getStatuses()) ?>

            <?= $form->field($model, 'yandex_tariffs')->widget(Select2::classname(), [
                'data' => OrderDeliveryInYandex::getYandexTariffs(),
                'options' => [
                    'placeholder' => Yii::t('country', 'Select yandex tariffs'),
                    'multiple' => true
                ],
            ]);?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

            <p id="notice"></p>

            <?= $form->field($model, 'latitude')->hiddenInput()->label(false) ?>

            <?= $form->field($model, 'longitude')->hiddenInput()->label(false) ?>

            <?php
            try {
                $placeMark['placeMark'] = [
                    'lat' => $model->latitude,
                    'lon' => $model->longitude,
                    'name' => $model->name,
                    'content' => sprintf(
                        '<h4>%s</h4><h6 class="small">%s<br/><abbr class="address-line full-width" title="%s">%s: </abbr><a href="tel:%s">%s</a></h6>',
                        Html::encode($model->name_organization),
                        Html::encode($model->address),
                        Yii::t('backend', 'Phone'),
                        Yii::t('backend', 'Phone'),
                        Html::encode($model->contact_phone),
                        Html::encode($model->contact_phone),
                    ),
                ];
            } catch (Exception $e) {
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
