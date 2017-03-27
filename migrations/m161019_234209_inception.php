<?php

use yii\db\Migration;

class m161019_234209_inception extends Migration
{
    public function up()
    {
        $this->createEventTypesTable();
        $this->createEventsTable();
    }

    private function createEventTypesTable()
    {
        $this->createTable('event_types', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(),
            'slug' => $this->string(),
        ]);
    }

    private function createEventsTable()
    {
        $this->createTable('events', [
            'id' => $this->primaryKey()->unsigned(),
            'title' => $this->string(),
            'description' => $this->text(),
            'timestamp' => $this->timestamp(),
            'type_id' => $this->integer()->unsigned()->null(),
        ]);

        $this->createIndex('idx_events_type_id', 'events', 'type_id');

        $this->addForeignKey('fk_events_type_id', 'events', 'type_id', 'event_types', 'id', 'SET NULL', 'SET NULL');
    }

    public function down()
    {
        $this->dropTable('events');
        $this->dropTable('event_types');

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
