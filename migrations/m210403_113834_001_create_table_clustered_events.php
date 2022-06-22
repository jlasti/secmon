<?php

use yii\db\Migration;

class m210403_113834_001_create_table_clustered_events extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%clustered_events}}', [
            'id' => $this->primaryKey(),
            'time' => $this->timestamp(),
            'raw' => $this->string(),
            'cluster_number' => $this->integer(),
            'cluster_run' => $this->integer(),
            'comment' => $this->string(),
        ], $tableOptions);

        $this->createIndex('idx_clustered_events_id', '{{%clustered_events}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%clustered_events}}');
    }
}
