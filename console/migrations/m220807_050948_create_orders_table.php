<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%orders}}`.
 */
class m220807_050948_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%orders}}', [
            'id' => $this->primaryKey(),
            'mobile_backend_id' => $this->string(255)->comment('Mobile backend id'),
            'mobile_backend_callback_url' => $this->string(255)->comment('Mobile backend callback url'),
            'client_phone' => $this->string(255)->notNull()->comment('Client phone'),
            'client_name' => $this->string(255)->comment('Client name'),
            'country_id' => $this->integer()->comment('Country'),
            'address' => $this->string(500)->comment('Address'),
            'latitude' => $this->string(100)->comment('Latitude'),
            'longitude' => $this->string(100)->comment('Longitude'),
            'entrance' => $this->string(100)->comment('Entrance'),
            'floor' => $this->string(100)->comment('Floor'),
            'flat' => $this->string(100)->comment('Flat'),
            'order_price' => $this->money()->defaultValue(0)->comment('Order price'),
            'payment_cash' => $this->money()->defaultValue(0)->comment('Payment cash'),
            'payment_bonuses' => $this->money()->defaultValue(0)->comment('Payment bonuses'),
            'payment_status' => $this->smallInteger()->notNull()->defaultValue(2)->comment('Payment status'),
            'comment_for_operator' => $this->string(500)->comment('Comment for operator'),
            'operator_deadline_date' => $this->integer()->comment('Operator deadline date'),
            'operator_real_date' => $this->integer()->comment('Operator real date'),
            'user_id_operator' => $this->integer()->comment('Operator'),
            'comment_for_shop_manager' => $this->string(500)->comment('Comment for shop manager'),
            'shop_manager_deadline_date' => $this->integer()->comment('Shop manager deadline date'),
            'shop_manager_real_date' => $this->integer()->comment('Shop manager real date'),
            'user_id_shop_manager' => $this->integer()->comment('Shop manager'),
            'comment_for_driver' => $this->string(500)->comment('Comment for driver'),
            'driver_deadline_date' => $this->integer()->comment('Driver deadline date'),
            'driver_real_date' => $this->integer()->comment('Driver real date'),
            'user_id_driver' => $this->integer()->comment('Driver'),
            'shop_id' => $this->integer()->comment('Shop'),
            'source_id' => $this->integer()->defaultValue(1)->comment('Source'),
            'delivery_type' => $this->smallInteger()->defaultValue(1)->comment('Delivery type'),
            'delivery_date' => $this->integer()->comment('Delivery date'),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('Status'),
            'created_at' => $this->integer()->notNull()->comment('Created date'),
            'updated_at' => $this->integer()->notNull()->comment('Updated date'),
        ]);

        // creates index for column `country_id`
        $this->createIndex(
            'idx-orders-country_id',
            'orders',
            'country_id'
        );

        // add foreign key for table `{{%countries}}`
        $this->addForeignKey(
            'fk-orders-country_id',
            'orders',
            'country_id',
            'countries',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // creates index for column `user_id_operator`
        $this->createIndex(
            'idx-orders-user_id_operator',
            'orders',
            'user_id_operator'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            'fk-orders-user_id_operator',
            'orders',
            'user_id_operator',
            'user',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // creates index for column `user_id_shop_manager`
        $this->createIndex(
            'idx-orders-user_id_shop_manager',
            'orders',
            'user_id_shop_manager'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            'fk-orders-user_id_shop_manager',
            'orders',
            'user_id_shop_manager',
            'user',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // creates index for column `user_id_driver`
        $this->createIndex(
            'idx-orders-user_id_driver',
            'orders',
            'user_id_driver'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            'fk-orders-user_id_driver',
            'orders',
            'user_id_driver',
            'user',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // creates index for column `shop_id`
        $this->createIndex(
            'idx-orders-shop_id',
            'orders',
            'shop_id'
        );

        // add foreign key for table `{{%shops}}`
        $this->addForeignKey(
            'fk-orders-shop_id',
            'orders',
            'shop_id',
            'shops',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // creates index for column `source_id`
        $this->createIndex(
            'idx-orders-source_id',
            'orders',
            'source_id'
        );

        // add foreign key for table `{{%sources}}`
        $this->addForeignKey(
            'fk-orders-source_id',
            'orders',
            'source_id',
            'sources',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%countries}}`
        $this->dropForeignKey(
            'fk-orders-country_id',
            'orders'
        );

        // drops index for column `country_id`
        $this->dropIndex(
            'idx-orders-country_id',
            'orders'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            'fk-orders-user_id_operator',
            'orders'
        );

        // drops index for column `user_id_operator`
        $this->dropIndex(
            'idx-orders-user_id_operator',
            'orders'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            'fk-orders-user_id_shop_manager',
            'orders'
        );

        // drops index for column `user_id_shop_manager`
        $this->dropIndex(
            'idx-orders-user_id_shop_manager',
            'orders'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            'fk-orders-user_id_driver',
            'orders'
        );

        // drops index for column `user_id_driver`
        $this->dropIndex(
            'idx-orders-user_id_driver',
            'orders'
        );

        // drops foreign key for table `{{%shops}}`
        $this->dropForeignKey(
            'fk-orders-shop_id',
            'orders'
        );

        // drops index for column `shop_id`
        $this->dropIndex(
            'idx-orders-shop_id',
            'orders'
        );

        // drops foreign key for table `{{%sources}}`
        $this->dropForeignKey(
            'fk-orders-source_id',
            'orders'
        );

        // drops index for column `source_id`
        $this->dropIndex(
            'idx-orders-source_id',
            'orders'
        );

        $this->dropTable('{{%orders}}');
    }
}
