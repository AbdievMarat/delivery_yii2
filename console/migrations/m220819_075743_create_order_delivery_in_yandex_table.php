<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_delivery_in_yandex}}`.
 */
class m220819_075743_create_order_delivery_in_yandex_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_delivery_in_yandex}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->comment('Order'),
            'yandex_id' => $this->string(100)->notNull()->comment('Yandex id'),
            'shop_id' => $this->integer()->comment('Shop'),
            'shop_address' => $this->string(500)->comment('Shop address'),
            'shop_latitude' => $this->string(100)->comment('Shop latitude'),
            'shop_longitude' => $this->string(100)->comment('Shop longitude'),
            'client_address' => $this->string(500)->comment('Client address'),
            'client_latitude' => $this->string(100)->comment('Client latitude'),
            'client_longitude' => $this->string(100)->comment('Client longitude'),
            'tariff' => $this->string(50)->comment('Tariff'),
            'offer_price' => $this->money()->comment('Offer price'),
            'final_price' => $this->money()->comment('Final price'),
            'driver_phone' => $this->string()->comment('Driver phone'),
            'driver_phone_ext' => $this->string()->comment('Driver phone ext'),
            'user_id' => $this->integer()->comment('User'),
            'status' => $this->string(20)->comment('Status'),
            'created_at' => $this->integer()->notNull()->comment('Created date'),
            'updated_at' => $this->integer()->notNull()->comment('Updated date'),
        ]);

        // creates index for column `order_id`
        $this->createIndex(
            'idx-order_delivery_in_yandex-order_id',
            'order_delivery_in_yandex',
            'order_id'
        );

        // add foreign key for table `{{%orders}}`
        $this->addForeignKey(
            'fk-order_delivery_in_yandex-order_id',
            'order_delivery_in_yandex',
            'order_id',
            'orders',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // creates index for column `shop_id`
        $this->createIndex(
            'idx-order_delivery_in_yandex-shop_id',
            'order_delivery_in_yandex',
            'shop_id'
        );

        // add foreign key for table `{{%shops}}`
        $this->addForeignKey(
            'fk-order_delivery_in_yandex-shop_id',
            'order_delivery_in_yandex',
            'shop_id',
            'shops',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // creates index for column `user_id`
        $this->createIndex(
            'idx-order_delivery_in_yandex-user_id',
            'order_delivery_in_yandex',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            'fk-order_delivery_in_yandex-user_id',
            'order_delivery_in_yandex',
            'user_id',
            'user',
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
        // drops foreign key for table `{{%orders}}`
        $this->dropForeignKey(
            'fk-order_delivery_in_yandex-order_id',
            'order_delivery_in_yandex'
        );

        // drops index for column `order_id`
        $this->dropIndex(
            'idx-order_delivery_in_yandex-order_id',
            'order_delivery_in_yandex'
        );

        // drops foreign key for table `{{%shops}}`
        $this->dropForeignKey(
            'fk-order_delivery_in_yandex-shop_id',
            'order_delivery_in_yandex'
        );

        // drops index for column `shop_id`
        $this->dropIndex(
            'idx-order_delivery_in_yandex-shop_id',
            'order_delivery_in_yandex'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            'fk-order_delivery_in_yandex-user_id',
            'order_delivery_in_yandex'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-order_delivery_in_yandex-user_id',
            'order_delivery_in_yandex'
        );

        $this->dropTable('{{%order_delivery_in_yandex}}');
    }
}
