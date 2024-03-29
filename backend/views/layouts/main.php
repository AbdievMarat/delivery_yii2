<?php

/** @var \yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => 'Kulikov.com',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark fixed-top',
            'style' => 'background-color: #9c27b0;'
        ],
    ]);

    $languageItem = new cetver\LanguageSelector\items\DropDownLanguageItem([
        'languages' => [
            'en-US' => '<i class="bi bi-translate"></i> English',
            'ru-RU' => '<i class="bi bi-translate"></i> Russian',
        ],
        'options' => ['encode' => false],
    ]);

    $menuItems = [
        ['label' => '<i class="bi bi-card-list"></i> ' . Yii::t('header', 'Orders'), 'icon' => '<i class="bi bi-globe"></i>', 'url' => ['/order/index']],
        ['label' => '<i class="bi bi-gear"></i> ' . Yii::t('header', 'Settings'),
            'items' => [
                ['label' => '<i class="bi bi-translate"></i> ' . Yii::t('header', 'Translate Manager'), 'url' => ['/translatemanager'], 'visible' => Yii::$app->user->can('translatemanager')],
            ],
        ],
        ['label' => '<i class="bi bi-journals"></i> ' . Yii::t('header', 'Directory'),
            'items' => [
                ['label' => '<i class="bi bi-people"></i> ' . Yii::t('user', 'Users'), 'url' => ['/user/manage/index'], 'visible' => Yii::$app->user->can('user.manage.index')],
                ['label' => '<i class="bi bi-file-earmark-text"></i> ' . Yii::t('source', 'Sources'), 'url' => ['/source/index'], 'visible' => Yii::$app->user->can('source.index')],
                ['label' => '<i class="bi bi-globe"></i> ' . Yii::t('country', 'Countries'), 'url' => ['/country/index'], 'visible' => Yii::$app->user->can('country.index')],
                ['label' => '<i class="bi bi-shop"></i> ' . Yii::t('shop', 'Shops'), 'url' => ['/shop/index'], 'visible' => Yii::$app->user->can('shop.index')],
            ],
        ],
    ];
    echo Nav::widget([
        'encodeLabels' => false,
        'options' => ['class' => 'navbar-nav me-auto mb-2 mb-md-0'],
        'items' => $menuItems,
    ]);
    if (Yii::$app->user->isGuest) {
        echo Html::tag('div',Html::a('Login',['/site/login'],['class' => ['btn btn-link login text-decoration-none']]),['class' => ['d-flex']]);
    } else {
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => [$languageItem->toArray()],
        ]);
        echo Html::beginForm(['/site/logout'], 'post', ['class' => 'd-flex'])
            . Html::submitButton(
                '<i class="bi bi-box-arrow-in-right"></i> Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-primary logout']
            )
            . Html::endForm();
    }
    NavBar::end();
    ?>
</header>

<?php
    $container_class = 'container';
    if(Yii::$app->controller->id == 'order' && (Yii::$app->controller->action->id == 'index' || Yii::$app->controller->action->id == 'create' || Yii::$app->controller->action->id == 'update'))
        $container_class = 'container-fluid';
?>

<main role="main" class="flex-shrink-0">
    <div class="<?= $container_class ?>">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p class="float-start">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
        <p class="float-end"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
