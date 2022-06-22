<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%events_normalized}}`.
 */
class m210508_145102_add_device_process_id_column_source_user_privileges_column_exec_user_column_to_events_normalized_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%events_normalized}}', 'device_process_id', $this->integer());
        $this->addColumn('{{%events_normalized}}', 'source_user_privileges', $this->text());
        $this->addColumn('{{%events_normalized}}', 'exec_user', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%events_normalized}}', 'device_process_id');
        $this->dropColumn('{{%events_normalized}}', 'source_user_privileges');
        $this->dropColumn('{{%events_normalized}}', 'exec_user');
    }
}
