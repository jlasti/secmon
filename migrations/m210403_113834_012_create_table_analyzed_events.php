<?php

use yii\db\Migration;

class m210403_113834_012_create_table_analyzed_events extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%analyzed_events}}', [
            'id' => $this->primaryKey(),
            'time' => $this->timestamp(),
            'events_normalized_id' => $this->integer(),
            'src_ip' => $this->string(),
            'dst_ip' => $this->string(),
            'code' => $this->string(),
            'country' => $this->string(),
            'city' => $this->string(),
            'src_city' => $this->string(),
            'src_code' => $this->string(),
            'latitude' => $this->double(),
            'longitude' => $this->double(),
            'src_latitude' => $this->double(),
            'src_longitude' => $this->double(),
            'events_count' => $this->integer(),
            'iteration' => $this->integer(),
            'flag' => $this->boolean(),
        ], $tableOptions);

        $this->createIndex('idx_analyzed_events_id', '{{%analyzed_events}}', 'id');
        $this->addForeignKey('fk_events_normalized_id', '{{%analyzed_events}}', 'events_normalized_id', '{{%events_normalized}}', 'id', 'SET NULL', 'SET NULL');
    }

    public function down()
    {
        $this->dropTable('{{%analyzed_events}}');
    }
}
