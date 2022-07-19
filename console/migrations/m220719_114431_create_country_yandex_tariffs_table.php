<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%country_yandex_tariffs}}`.
 */
class m220719_114431_create_country_yandex_tariffs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%country_yandex_tariffs}}', [
            'id' => $this->primaryKey(),
            'country_id' => $this->integer()->notNull()->comment('Country'),
            'name_tariff' => $this->string()->notNull()->comment('Name tariff'),
        ]);

        // creates index for column `country_id`
        $this->createIndex(
            'idx-country_yandex_tariffs-country_id',
            'country_yandex_tariffs',
            'country_id'
        );

        // add foreign key for table `{{%countries}}`
        $this->addForeignKey(
            'fk-country_yandex_tariffs-country_id',
            'country_yandex_tariffs',
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
        $this->dropTable('{{%country_yandex_tariffs}}');

        // drops index for column `country_id`
        $this->dropIndex(
            'idx-country_yandex_tariffs-country_id',
            'country_yandex_tariffs'
        );

        // drops foreign key for table `{{%countries}}`
        $this->dropForeignKey(
            'fk-country_yandex_tariffs-country_id',
            'country_yandex_tariffs'
        );
    }
}
