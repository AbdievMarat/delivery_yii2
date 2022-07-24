<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sources}}`.
 */
class m220630_113242_create_sources_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sources}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('Name'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('Status'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sources}}');
    }
}
