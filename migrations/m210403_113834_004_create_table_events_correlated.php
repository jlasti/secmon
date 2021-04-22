<?php

use yii\db\Migration;

class m210403_113834_004_create_table_events_correlated extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%events_correlated}}', [
            'id' => $this->bigPrimaryKey(),
            'datetime' => $this->timestamp(),
            'host' => $this->string(),
            'cef_version' => $this->string()->notNull(),
            'cef_vendor' => $this->string()->notNull(),
            'cef_dev_prod' => $this->string()->notNull(),
            'cef_dev_version' => $this->string()->notNull(),
            'cef_event_class_id' => $this->integer()->notNull(),
            'cef_name' => $this->string()->notNull(),
            'cef_severity' => $this->integer()->notNull(),
            'parent_events' => $this->text(),
            'raw' => $this->text(),
            'attack_type' => $this->string(),
        ], $tableOptions);

        $this->createIndex('idx_events_corr_datetime', '{{%events_correlated}}', 'datetime');
        $this->createIndex('idx_events_corr_host', '{{%events_correlated}}', 'host');
        $this->createIndex('idx_events_corr_cef_vendor', '{{%events_correlated}}', 'cef_vendor');
        $this->createIndex('idx_events_corr_cef_dev_prod', '{{%events_correlated}}', 'cef_dev_prod');
        $this->createIndex('idx_events_corr_cef_name', '{{%events_correlated}}', 'cef_name');
        $this->createIndex('idx_events_corr_cef_severity', '{{%events_correlated}}', 'cef_severity');
    }

    public function down()
    {
        $this->dropTable('{{%events_correlated}}');
    }
}
