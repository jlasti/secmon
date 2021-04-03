<?php

use yii\db\Migration;

class m210403_113834_014_create_table_clustered_events_clusters extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%clustered_events_clusters}}', [
            'id' => $this->primaryKey(),
            'severity' => $this->integer(),
            'comment' => $this->string(),
            'fk_run_id' => $this->integer()->notNull(),
            'number_of_events' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('fk_clustered_events_clusters', '{{%clustered_events_clusters}}', 'fk_run_id', '{{%clustered_events_runs}}', 'id', 'CASCADE', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%clustered_events_clusters}}');
    }
}
