<?php

use yii\db\Migration;

/**
 * Class m220830_214915_drop_table_analyzed_normalized_events_list
 */
class m220830_214915_drop_table_analyzed_normalized_events_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%analyzed_normalized_events_list}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220830_214915_drop_table_analyzed_normalized_events_list cannot be reverted.\n";

        return false;
    }
}
