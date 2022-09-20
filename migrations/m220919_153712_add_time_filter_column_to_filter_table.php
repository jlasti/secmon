<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%filter}}`.
 */
class m220919_153712_add_time_filter_column_to_filter_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%filters}}', 'time_filter', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%filters}}', 'time_filter');
    }
}
