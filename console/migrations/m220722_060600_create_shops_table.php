<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shops}}`.
 */
class m220722_060600_create_shops_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shops}}', [
            'id' => $this->primaryKey(),
            'country_id' => $this->integer()->notNull()->comment('Country'),
            'name' => $this->string(255)->notNull()->comment('Name'),
            'contact_phone' => $this->string(255)->comment('Contact phone'),
            'address' => $this->string(500)->notNull()->comment('Address'),
            'latitude' => $this->string(100)->notNull()->comment('Latitude'),
            'longitude' => $this->string(100)->notNull()->comment('Longitude'),
            'mobile_backend_id' => $this->string(255)->unique()->comment('Mobile backend id'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('Status'),
        ]);

        // creates index for column `country_id`
        $this->createIndex(
            'idx-shops-country_id',
            'shops',
            'country_id'
        );

        // add foreign key for table `{{%countries}}`
        $this->addForeignKey(
            'fk-shops-country_id',
            'shops',
            'country_id',
            'countries',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shops}}');

        // drops index for column `country_id`
        $this->dropIndex(
            'idx-shops-country_id',
            'shops'
        );

        // drops foreign key for table `{{%countries}}`
        $this->dropForeignKey(
            'fk-shops-country_id',
            'shops'
        );
    }
}
