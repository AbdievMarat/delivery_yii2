<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%counties}}`.
 */
class m220702_044442_create_counties_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%counties}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('Name'),
            'name_currency' => $this->string(255)->comment('Name currency'),
            'name_organization' => $this->string(255)->comment('Name organization'),
            'contact_phone' => $this->string(255)->comment('Contact phone'),
            'token_yandex' => $this->string(255)->comment('Token yandex'),
            'token_mobile_backend' => $this->string(255)->comment('Token mobile backend'),
            'latitude' => $this->string(100)->comment('Latitude'),
            'longitude' => $this->string(100)->comment('Longitude'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('Status'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%counties}}');
    }
}
