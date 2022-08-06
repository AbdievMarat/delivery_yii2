<?php

use backend\models\Country;
use common\models\User;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\bootstrap5\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'autocomplete' => 'off']) ?>

            <?= $form->field($model, 'email') ?>

            <?= $form->field($model, 'password_form')->passwordInput() ?>

            <?= $form->field($model, 'status')->dropDownList(User::getStatuses()) ?>

            <?= $form->field($model, 'role')->dropDownList(User::getRoles()) ?>

            <?= $form->field($model, 'available_countries')->widget(Select2::classname(), [
                'data' => Country::getCountriesList(),
                'options' => [
                    'placeholder' => Yii::t('user', 'Select countries'),
                    'multiple' => true
                ],
            ]);?>
        </div>
        <div class="col-md-4">
            <label class="form-label"><?= Yii::t('user', 'Avatar')?></label>

            <?= $form->field($model, 'file')->widget(FileInput::classname(), [
                'pluginOptions' => [
                    'showClose' => false,
                    'showCaption' => false,
                    'showRemove' => true,
                    'showUpload' => false,
                    'showPreview' => true,
                    'browseOnZoneClick' => true,
                    'showBrowse' => true,
                    'removeLabel' => '',
                    'removeIcon' => '<i class="bi bi-trash-fill"></i>',
                    'removeTitle' => 'Cancel or reset changes',
                    'previewFileType' => 'any',
                    'initialPreviewAsData' => true,
                    'initialPreview' => [
                        $model->getAvatarImagePreview()
                    ],
                    'initialPreviewConfig' => [
                        [
                            'url' => 'drop-image', // server delete action
                            'key' => $model->id ?? null,
                        ]
                    ],
                    'layoutTemplates' => '{main: "{preview} " +  btnCust + " {remove} {browse}"}',
                ],
                'options' => [
                    'accept' => 'image/*',
                    'multiple' => false
                ],
            ])->label(false); ?>
        </div>
    </div>

    <div class="mb-3">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success w-100' : 'btn btn-primary w-100']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
