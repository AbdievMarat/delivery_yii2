<?php

use backend\models\Country;
use backend\models\Source;
use backend\widgets\YandexMap;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelOrder backend\models\Order */
/* @var $modelsOrderItem backend\models\OrderItem */
/* @var $form yii\bootstrap5\ActiveForm */

$this->registerJsFile('@web/js/order/scripts.js', ['depends' => [
    \yii\web\JqueryAsset::className(),
    \yii\web\YiiAsset::className(),
    \yii\jui\JuiAsset::className()
]]);

$this->registerCssFile('@web/css/order/style.css');
?>
<!--
<div class="container-fluid pt-5 pb-4">

    <div class="row">
        <div class="col-lg-12">

            <div class="horizontal-timeline">

                <ul class="list-inline items">
                    <li class="list-inline-item items-list">
                        <div class="px-4">
                            <div class="event-date badge bg-warning rounded-pill fs-6"><i class="bi bi-headset"></i> Оператор</div>
                            <h5 class="pt-2"><i class="bi bi-hand-thumbs-up-fill"></i> 00:12:25</h5>
                            <p class="text-muted"> Захарова Анастасия</p>
                        </div>
                    </li>
                    <li class="list-inline-item items-list">
                        <div class="px-4">
                            <div class="event-date badge bg-info rounded-pill fs-6"><i class="bi bi-shop"></i> Магазин</div>
                            <h5 class="pt-2"><i class="bi bi-hand-thumbs-down-fill"></i> 00:42:15</h5>
                            <p class="text-muted"> ФМ "САТПАЕВА"</p>
                        </div>
                    </li>
                    <li class="list-inline-item items-list">
                        <div class="px-4">
                            <div class="event-date badge bg-primary rounded-pill fs-6"><i class="bi bi-car-front"></i> Курьер</div>
                            <h5 class="pt-2"><i class="bi bi-hand-thumbs-up-fill"></i> 00:00:55</h5>
                            <p class="text-muted">Yandex</p>
                        </div>
                    </li>
                    <li class="list-inline-item items-list">
                        <div class="px-4">
                            <div class="event-date badge bg-success rounded-pill fs-6"><i class="bi bi-flag"></i> Доставлен</div>
                            <h5 class="pt-2"><i class="bi bi-clock-fill"></i> 00:49:55</h5>
                            <p class="text-muted">Общее время</p>
                        </div>
                    </li>
                    <li class="list-inline-item items-list">
                        <div class="px-4">
                            <div class="event-date badge bg-danger rounded-pill fs-6"><i class="bi bi-x-circle"></i> Отменен</div>
                            <h5 class="pt-2">16-09-2022 03:44</h5>
                            <p class="text-muted">Захарова Анастасия</p>
                        </div>
                    </li>
                </ul>

            </div>

        </div>
    </div>

</div>
-->
<div class="order-form">

    <?php $form = ActiveForm::begin(['id' => 'order-dynamic-form']); ?>

    <div class="row">
        <div class="col-md-4">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamic_form_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 20, // the maximum times, an element can be cloned (default 999)
                'min' => 0, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsOrderItem[0],
                'formId' => 'order-dynamic-form',
                'formFields' => [
                    'product_name',
                    'product_code',
                    'product_price',
                    'amount',
                ],
            ]); ?>
            <div class="card mb-3">
                <div class="card-header">
                    <h4>
                        <?= Yii::t('order', 'Product list') ?>
                        <button type="button" class="add-item btn btn-success float-end">+</button>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-1">
                        <div class="col-md-6">
                            <label class="form-label"><?= Yii::t('order_item', 'Product name') ?></label>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= Yii::t('order_item', 'Product price') ?></label>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><?= Yii::t('order_item', 'Amount') ?></label>
                        </div>
                    </div>
                    <div class="container-items"><!-- widgetContainer -->
                        <?php foreach ($modelsOrderItem as $i => $modelOrderItem): ?>
                            <div class="row g-1 item">
                                <?php
                                // necessary for update action.
                                if (!$modelOrderItem->isNewRecord) {
                                    echo Html::activeHiddenInput($modelOrderItem, "[{$i}]id");
                                }
                                ?>
                                <div class="col-md-6">
                                    <?= $form->field($modelOrderItem, "[{$i}]product_name", ['inputOptions' => ['class' => 'form-control product-search']])->textInput()->label(false) ?>
                                    <?= $form->field($modelOrderItem, "[{$i}]product_code")->hiddenInput()->label(false) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->field($modelOrderItem, "[{$i}]product_price")->textInput(['readonly' => true])->label(false) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($modelOrderItem, "[{$i}]amount", ['inputOptions' => ['class' => 'form-control product-amount']])->textInput(['type' => 'number'])->label(false) ?>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="remove-item btn btn-danger">-</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php DynamicFormWidget::end(); ?>

            <div id="remains_products">

            </div>

            <?= $form->field($modelOrder, 'id')->hiddenInput()->label(false) ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($modelOrder, 'order_price')->textInput(['maxlength' => true, 'readonly' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($modelOrder, 'payment_bonuses')->textInput(['maxlength' => true, 'type' => 'number']) ?>
                </div>
            </div>

            <?= $form->field($modelOrder, 'payment_cash')->textInput(['maxlength' => true, 'type' => 'number']) ?>

            <?= $form->field($modelOrder, 'payment_status')->dropDownList($modelOrder->getPaymentStatuses()) ?>

            <?= $form->field($modelOrder, 'status')->dropDownList($modelOrder->getStatuses()) ?>

            <?= $form->field($modelOrder, 'mobile_backend_id')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($modelOrder, 'client_phone')->textInput() ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($modelOrder, 'client_name')->textInput() ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($modelOrder, 'country_id')->dropDownList(Country::getAvailableCountriesToUser()) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($modelOrder, 'shop_id')->dropDownList([]) ?>
                    <input type="hidden" id="order-shop_id_value" value="<?= $modelOrder->shop_id ?>">
                </div>
            </div>

            <?= $form->field($modelOrder, 'address')->textInput(['maxlength' => true]) ?>

            <p id="notice"></p>

            <?php
            try {
                $placeMark['placeMark'] = [
                    'lat' => $modelOrder->latitude,
                    'lon' => $modelOrder->longitude,
                    'name' => $modelOrder->client_name,
                    'content' => sprintf(
                        '<h4>%s</h4><h6 class="small">%s<br/><abbr class="address-line full-width" title="%s">%s: </abbr><a href="tel:%s">%s</a></h6>',
                        Html::encode($modelOrder->client_phone),
                        Html::encode($modelOrder->comment_for_driver),
                        Yii::t('backend', 'Phone'),
                        Yii::t('backend', 'Phone'),
                        Html::encode($modelOrder->client_phone),
                        Html::encode($modelOrder->client_phone),
                    ),
                ];
            } catch (Exception $e) {
                $placeMark = [];
            }

            $defaultsForMap = [
                'formIdAddress' => Html::getInputId($modelOrder, 'address'),
                'formIdLatitude' => Html::getInputId($modelOrder, 'latitude'),
                'formIdLongitude' => Html::getInputId($modelOrder, 'longitude'),
            ];

            echo YandexMap::widget(array_merge($placeMark, $defaultsForMap));
            ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($modelOrder, 'latitude')->hiddenInput()->label(false) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($modelOrder, 'longitude')->hiddenInput()->label(false) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($modelOrder, 'entrance')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($modelOrder, 'floor')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($modelOrder, 'flat')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($modelOrder, 'comment_for_operator')->textarea(['maxlength' => true, 'placeholder' => Yii::t('order', 'Comment for operator')])->label(false) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($modelOrder, 'comment_for_shop_manager')->textarea(['maxlength' => true, 'placeholder' => Yii::t('order', 'Comment for shop manager')])->label(false) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($modelOrder, 'comment_for_driver')->textarea(['maxlength' => true, 'placeholder' => Yii::t('order', 'Comment for driver')])->label(false) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($modelOrder, 'delivery_type')->dropDownList($modelOrder->getTypesDelivery()) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($modelOrder, 'delivery_date')->textInput() ?>
                </div>
            </div>

            <?= $form->field($modelOrder, 'source_id')->dropDownList(Source::getSourcesList()) ?>
        </div>

        <div class="col-md-4">
            <input type="hidden" id="order-count_of_deliveries" value="<?= $modelOrder->getOrderDeliveryInYandex()->count() ?>">

            <ol class="list-group mb-3" id="orders-delivery-in-yandex"></ol>

            <?= Html::button(Yii::t('backend', 'Create order in Yandex delivery') . ' <span class="span_spinner"></span>', ['class' => 'btn btn-success w-100 create-order-yandex']) ?>
        </div>
    </div>

    <div class="mb-3 pt-3">
        <?= Html::submitButton($modelOrder->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $modelOrder->isNewRecord ? 'btn btn-success w-100' : 'btn btn-primary w-100']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
