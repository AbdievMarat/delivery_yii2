<?php

use backend\widgets\YandexMap;
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

            <?= $form->field($model, 'status')->dropDownList(\backend\models\Country::getStatuses()) ?>
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

            <label class="control-label">Map</label>

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
                } catch (\Exception $e) {
                    $placeMark = [];
                }

                $defaultsForMap = [
                    'formIdAddress' => Html::getInputId($model, 'address'),
                    'formIdLatitude' => Html::getInputId($model, 'latitude'),
                    'formIdLonitude' => Html::getInputId($model, 'longitude'),
                ];

                echo YandexMap::widget(array_merge($placeMark, $defaultsForMap));
            ?>
        </div>
    </div>

    <div class="col-md-12 form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-group-justified btn-success' : 'btn btn-group-justified btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
