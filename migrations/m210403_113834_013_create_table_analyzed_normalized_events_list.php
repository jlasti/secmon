<?php

use yii\db\Migration;

class m210403_113834_013_create_table_analyzed_normalized_events_list extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%analyzed_normalized_events_list}}', [
            'id' => $this->primaryKey(),
            'events_analyzed_iteration' => $this->integer(),
            'events_normalized_id' => $this->integer(),
            'events_analyzed_normalized_id' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx_analyzed_normalized_events_list_id', '{{%analyzed_normalized_events_list}}', 'id');
        $this->addForeignKey('fk_events_analyzed_normalized_id', '{{%analyzed_normalized_events_list}}', 'events_analyzed_normalized_id', '{{%events_normalized}}', 'id', 'SET NULL', 'SET NULL');
    }

    public function down()
    {
        $this->dropTable('{{%analyzed_normalized_events_list}}');
    }
}
