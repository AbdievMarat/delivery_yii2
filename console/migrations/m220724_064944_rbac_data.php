<?php

use yii\db\Migration;

/**
 * Class m220724_064944_rbac_data
 */
class m220724_064944_rbac_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $auth = Yii::$app->authManager;

        // define permission in Country Controller
        $countryIndex = $auth->createPermission('country.index');
        $auth->add($countryIndex);

        $countryCreate = $auth->createPermission('country.create');
        $auth->add($countryCreate);

        $countryView = $auth->createPermission('country.view');
        $auth->add($countryView);

        $countryUpdate = $auth->createPermission('country.update');
        $auth->add($countryUpdate);

        $countryDelete = $auth->createPermission('country.delete');
        $auth->add($countryDelete);

        // define permission in Source Controller
        $sourceIndex = $auth->createPermission('source.index');
        $auth->add($sourceIndex);

        $sourceCreate = $auth->createPermission('source.create');
        $auth->add($sourceCreate);

        $sourceView = $auth->createPermission('source.view');
        $auth->add($sourceView);

        $sourceUpdate = $auth->createPermission('source.update');
        $auth->add($sourceUpdate);

        $sourceDelete = $auth->createPermission('source.delete');
        $auth->add($sourceDelete);

        // define permission in Shop Controller
        $shopIndex = $auth->createPermission('shop.index');
        $auth->add($shopIndex);

        $shopCreate = $auth->createPermission('shop.create');
        $auth->add($shopCreate);

        $shopView = $auth->createPermission('shop.view');
        $auth->add($shopView);

        $shopUpdate = $auth->createPermission('shop.update');
        $auth->add($shopUpdate);

        $shopDelete = $auth->createPermission('shop.delete');
        $auth->add($shopDelete);

        // define permission in modules/Manage Controller
        $userManageIndex = $auth->createPermission('user.manage.index');
        $auth->add($userManageIndex);

        $userManageCreate = $auth->createPermission('user.manage.create');
        $auth->add($userManageCreate);

        $userManageView = $auth->createPermission('user.manage.view');
        $auth->add($userManageView);

        $userManageUpdate = $auth->createPermission('user.manage.update');
        $auth->add($userManageUpdate);

        $userManageDelete = $auth->createPermission('user.manage.delete');
        $auth->add($userManageDelete);

        // define permission in lajax\translatemanager\Module
        $translateManager = $auth->createPermission('translatemanager');
        $auth->add($translateManager);

        // define roles
        $admin = $auth->createRole('admin');
        $auth->add($admin);

        $manager = $auth->createRole('manager');
        $auth->add($manager);

        $operator = $auth->createRole('operator');
        $auth->add($operator);

        $shopManager = $auth->createRole('shop_manager');
        $auth->add($shopManager);

        $accountant = $auth->createRole('accountant');
        $auth->add($accountant);

        // define admin role - permissions relations
        $auth->addChild($admin, $countryIndex);
        $auth->addChild($admin, $countryCreate);
        $auth->addChild($admin, $countryView);
        $auth->addChild($admin, $countryUpdate);
        $auth->addChild($admin, $countryDelete);

        $auth->addChild($admin, $sourceIndex);
        $auth->addChild($admin, $sourceCreate);
        $auth->addChild($admin, $sourceView);
        $auth->addChild($admin, $sourceUpdate);
        $auth->addChild($admin, $sourceDelete);

        $auth->addChild($admin, $shopIndex);
        $auth->addChild($admin, $shopCreate);
        $auth->addChild($admin, $shopView);
        $auth->addChild($admin, $shopUpdate);
        $auth->addChild($admin, $shopDelete);

        $auth->addChild($admin, $userManageIndex);
        $auth->addChild($admin, $userManageCreate);
        $auth->addChild($admin, $userManageView);
        $auth->addChild($admin, $userManageUpdate);
        $auth->addChild($admin, $userManageDelete);

        $auth->addChild($admin, $translateManager);

        // define manager role - permissions relations
        $auth->addChild($manager, $countryIndex);
        $auth->addChild($manager, $countryView);

        $auth->addChild($manager, $sourceIndex);
        $auth->addChild($manager, $sourceView);

        $auth->addChild($manager, $shopIndex);
        $auth->addChild($manager, $shopCreate);
        $auth->addChild($manager, $shopView);
        $auth->addChild($manager, $shopUpdate);

        $auth->addChild($manager, $userManageIndex);
        $auth->addChild($manager, $userManageCreate);
        $auth->addChild($manager, $userManageView);
        $auth->addChild($manager, $userManageUpdate);

        // define operator role - permissions relations
        $auth->addChild($operator, $countryIndex);
        $auth->addChild($operator, $countryView);

        $auth->addChild($operator, $sourceIndex);
        $auth->addChild($operator, $sourceView);

        $auth->addChild($operator, $shopIndex);
        $auth->addChild($operator, $shopCreate);
        $auth->addChild($operator, $shopView);
        $auth->addChild($operator, $shopUpdate);

        $auth->addChild($operator, $userManageIndex);
        $auth->addChild($operator, $userManageView);

        $auth->assign($admin, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();
    }
}
