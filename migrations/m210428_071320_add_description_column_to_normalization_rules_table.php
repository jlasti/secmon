<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%normalization_rules}}`.
 */
class m210428_071320_add_description_column_to_normalization_rules_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%normalization_rules}}', 'description', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%normalization_rules}}', 'description');
    }
}
