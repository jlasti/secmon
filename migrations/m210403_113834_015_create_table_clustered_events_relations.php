<?php

use yii\db\Migration;

class m210403_113834_015_create_table_clustered_events_relations extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%clustered_events_relations}}', [
            'id' => $this->primaryKey(),
            'fk_run_id' => $this->integer()->notNull(),
            'fk_cluster_id' => $this->integer()->notNull(),
            'fk_event_id' => $this->bigInteger()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_clustered_events_relations_run', '{{%clustered_events_relations}}', 'fk_run_id', '{{%clustered_events_runs}}', 'id', 'CASCADE', 'NO ACTION');
        $this->addForeignKey('fk_clustered_events_relations_cluster', '{{%clustered_events_relations}}', 'fk_cluster_id', '{{%clustered_events_clusters}}', 'id', 'CASCADE', 'NO ACTION');
        $this->addForeignKey('fk_clustered_events_relations_event', '{{%clustered_events_relations}}', 'fk_event_id', '{{%events_normalized}}', 'id', 'CASCADE', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%clustered_events_relations}}');
    }
}
