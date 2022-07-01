<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sourses}}`.
 */
class m220630_113242_create_sourses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sourses}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('Name'),
            'status' => $this->tinyInteger()->defaultValue(1)->comment('Status'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sourses}}');
    }
}
