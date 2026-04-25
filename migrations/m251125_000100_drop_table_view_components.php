<?php

use yii\db\Migration;

/**
 * Class m220831_074323_drop_table_events_correlated
 */
class m251125_000100_drop_table_view_components extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%view_components}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251125_000100_drop_table_view_components cannot be reverted.\n";

        return false;
    }
}
