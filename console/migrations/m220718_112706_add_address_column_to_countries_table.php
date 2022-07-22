<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%countries}}`.
 */
class m220718_112706_add_address_column_to_countries_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%countries}}', 'address', $this->string(500)->after('token_mobile_backend')->comment('Address'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%countries}}', 'address');
    }
}
