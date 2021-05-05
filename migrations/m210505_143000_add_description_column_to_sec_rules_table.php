<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%sec_rules}}`.
 */
class m210505_143000_add_description_column_to_sec_rules_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sec_rules}}', 'description', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%sec_rules}}', 'description');
    }
}
