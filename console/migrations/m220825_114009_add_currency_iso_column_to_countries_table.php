<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%coutries}}`.
 */
class m220825_114009_add_currency_iso_column_to_countries_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%countries}}', 'currency_iso', $this->string(3)->after('name_currency')->comment('Currency ISO'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%countries}}', 'currency_iso');
    }
}
