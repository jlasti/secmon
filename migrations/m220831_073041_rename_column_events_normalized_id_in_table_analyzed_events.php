<?php

use yii\db\Migration;

/**
 * Class m220831_073041_rename_column_events_normalized_id_in_table_analyzed_events
 */
class m220831_073041_rename_column_events_normalized_id_in_table_analyzed_events extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%analyzed_events}}', 'events_normalized_id', 'security_events_id');
        $this->dropForeignKey('fk_events_normalized_id', '{{%analyzed_events}}');
        $this->addForeignKey('fk_security_events_id', '{{%analyzed_events}}', 'security_events_id', '{{%security_events}}', 'id', 'SET NULL', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('{{%analyzed_events}}', 'security_events_id', 'events_normalized_id');
        $this->dropForeignKey('fk_security_events_id', '{{%analyzed_events}}');
        $this->addForeignKey('fk_events_normalized_id', '{{%analyzed_events}}', 'security_events_id', '{{%security_events}}', 'id', 'SET NULL', 'SET NULL');
    }
}
