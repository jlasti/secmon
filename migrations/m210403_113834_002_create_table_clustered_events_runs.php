<?php

use yii\db\Migration;

class m210403_113834_002_create_table_clustered_events_runs extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%clustered_events_runs}}', [
            'id' => $this->primaryKey(),
            'datetime' => $this->timestamp(),
            'type_of_algorithm' => $this->string(),
            'number_of_clusters' => $this->integer(),
            'comment' => $this->string(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%clustered_events_runs}}');
    }
}
