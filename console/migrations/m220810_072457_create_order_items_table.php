<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_items}}`.
 */
class m220810_072457_create_order_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_items}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull()->comment('Order'),
            'product_code' => $this->string(255)->notNull()->comment('Product code'),
            'product_name' => $this->string(255)->notNull()->comment('Product name'),
            'product_price' => $this->money()->comment('Product price'),
            'amount' => $this->integer()->notNull()->comment('Amount'),
            'created_at' => $this->integer()->notNull()->comment('Created date'),
            'updated_at' => $this->integer()->notNull()->comment('Updated date'),
        ]);

        // creates index for column `order_id`
        $this->createIndex(
            'idx-order_items-order_id',
            'order_items',
            'order_id'
        );

        // add foreign key for table `{{%orders}}`
        $this->addForeignKey(
            'fk-order_items-order_id',
            'order_items',
            'order_id',
            'orders',
            'id',
            'CASCADE',
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
            'fk-order_items-order_id',
            'order_items'
        );

        // drops index for column `order_id`
        $this->dropIndex(
            'idx-order_items-order_id',
            'order_items'
        );

        $this->dropTable('{{%order_items}}');
    }
}
