<?php

use yii\db\Migration;

/**
 * Class m220831_074502_drop_table_events_normalized
 */
class m220831_074502_drop_table_events_normalized extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%events_normalized}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220831_074502_drop_table_events_normalized cannot be reverted.\n";

        return false;
    }
}
