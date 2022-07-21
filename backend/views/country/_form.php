<?php

use backend\widgets\YandexMap;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

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

            <?= $form->field($model, 'name_organization')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'contact_phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'token_yandex')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'token_mobile_backend')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'status')->dropDownList(\backend\models\Country::getStatuses()) ?>

            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 3, // the maximum times, an element can be cloned (default 999)
                'min' => 0, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsYandexTariff[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'name_tariff',
                ],
            ]);?>
            <div class="card mb-3">
                <div class="card-header">
                    <h4>
                        <?= Yii::t('country_yandex_tariffs', 'Yandex tariffs') ?>
                        <button type="button" class="add-item btn btn-success btn-sm float-end">+</button>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="container-items"><!-- widgetContainer -->
                        <?php foreach ($modelsYandexTariff as $i => $modelYandexTariff): ?>
                            <div class="item card mb-3"><!-- widgetBody -->
                                <div class="card-header">
                                    <h3 class="card-title float-start"><?= Yii::t('country_yandex_tariffs', 'Tariff') ?></h3>
                                    <div class="float-end">
                                        <button type="button" class="remove-item btn btn-danger btn-sm">-</button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // necessary for update action.
                                    if (! $modelYandexTariff->isNewRecord) {
                                        echo Html::activeHiddenInput($modelYandexTariff, "[{$i}]id");
                                    }
                                    ?>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?= $form->field($modelYandexTariff, "[{$i}]name_tariff")->dropDownList(\backend\models\CountryYandexTariff::getTariffs()) ?>
                                        </div>
                                    </div><!-- .row -->
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php DynamicFormWidget::end(); ?>
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
        <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success w-100' : 'btn btn-primary w-100']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
