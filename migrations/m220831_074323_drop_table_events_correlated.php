<?php

use yii\db\Migration;

/**
 * Class m220831_074323_drop_table_events_correlated
 */
class m220831_074323_drop_table_events_correlated extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%events_correlated}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220831_074323_drop_table_events_correlated cannot be reverted.\n";

        return false;
    }
}
