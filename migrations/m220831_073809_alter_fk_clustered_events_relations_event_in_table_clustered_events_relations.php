<?php

use yii\db\Migration;

/**
 * Class m220831_073809_alter_fk_clustered_events_relations_event_in_table_clustered_events_relations
 */
class m220831_073809_alter_fk_clustered_events_relations_event_in_table_clustered_events_relations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_clustered_events_relations_event', '{{%clustered_events_relations}}');
        $this->addForeignKey('fk_clustered_events_relations_event', '{{%clustered_events_relations}}', 'fk_event_id', '{{%security_events}}', 'id', 'CASCADE', 'NO ACTION');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_clustered_events_relations_event', '{{%clustered_events_relations}}');
        $this->addForeignKey('fk_clustered_events_relations_event', '{{%clustered_events_relations}}', 'fk_event_id', '{{%events_normalized}}', 'id', 'CASCADE', 'NO ACTION');
    }
}
