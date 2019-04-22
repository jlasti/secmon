<?php

use yii\db\Migration;

/**
 * Class m181021_144913_create_analyzed_events
 */
class m181021_144913_create_analyzed_events extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createAnalyzedEventsTable();
        $this->createAnalyzedEventsList();
        $this->createClusteredEvents();
    }

    /**
     *
     */
    private function createAnalyzedEventsTable()
    {
        $this->createTable('analyzed_events', [
            'id' => $this->primaryKey()->unsigned(),
            'time' => $this->dateTime(),
            'events_normalized_id' => $this->integer(),
            'src_ip' => $this->string(),
            'dst_ip' => $this->string(),
            'code' => $this->string(),
            'country' => $this->string(),
            'city' => $this->string(),
            'src_city' => $this->string(),
            'src_code' => $this->string(),
            'latitude' => $this->float(),
            'longitude' => $this->float(),
            'src_latitude' => $this->float(),
            'src_longitude' => $this->float(),
            'events_count' => $this->integer(),
            'iteration' => $this->integer(),
            'flag' => $this->boolean(),
        ]);

        $this->addForeignKey('fk_events_normalized_id', 'analyzed_events', 'events_normalized_id', 'events_normalized', 'id', 'SET NULL', 'SET NULL');

        $this->createIndex('idx_analyzed_events_id', 'analyzed_events', 'id');
    }

    /**
     *
     */
    private function createAnalyzedEventsList()
    {
        $this->createTable('analyzed_normalized_events_list', [
            'id' => $this->primaryKey()->unsigned(),
            'events_analyzed_iteration' => $this->integer(),
            'events_normalized_id' => $this->integer(),
            'events_analyzed_normalized_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk_events_analyzed_normalized_id', 'analyzed_normalized_events_list', 'events_analyzed_normalized_id', 'events_normalized', 'id', 'SET NULL', 'SET NULL');

        $this->createIndex('idx_analyzed_normalized_events_list_id', 'analyzed_normalized_events_list', 'id');
    }

    /**
     *
     */
    private function createClusteredEvents()
    {
        $this->createTable('clustered_events', [
            'id' => $this->primaryKey()->unsigned(),
            'time' => $this->dateTime(),
            'raw' => $this->string(),
            'cluster_number' => $this->integer(),
            'cluster_run' => $this->integer(),
            'comment' => $this->string(),
        ]);

        $this->createIndex('idx_clustered_events_id', 'clustered_events', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('analyzed_events');
        $this->dropTable('analyzed_normalized_events_list');
        $this->dropTable('clustered_events');
        echo "m181021_144913_create_analyzed_events cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181021_144913_create_brute_force cannot be reverted.\n";

        return false;
    }
    */
}
