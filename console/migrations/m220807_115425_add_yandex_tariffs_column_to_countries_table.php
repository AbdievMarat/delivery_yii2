<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%countries}}`.
 */
class m220807_115425_add_yandex_tariffs_column_to_countries_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%countries}}', 'yandex_tariffs', $this->string(250)->after('longitude')->comment('Yandex tariffs'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%countries}}', 'yandex_tariffs');
    }
}
