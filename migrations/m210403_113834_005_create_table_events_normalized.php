<?php

use yii\db\Migration;

class m210403_113834_005_create_table_events_normalized extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%events_normalized}}', [
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
            'src_ip' => $this->string(),
            'dst_ip' => $this->string(),
            'src_port' => $this->integer(),
            'dst_port' => $this->integer(),
            'protocol' => $this->string(),
            'src_mac' => $this->string(),
            'dst_mac' => $this->string(),
            'extensions' => $this->text(),
            'raw' => $this->text(),
            'src_code' => $this->string(),
            'dst_code' => $this->string(),
            'src_country' => $this->string(),
            'dst_country' => $this->string(),
            'src_city' => $this->string(),
            'dst_city' => $this->string(),
            'src_latitude' => $this->double(),
            'dst_latitude' => $this->double(),
            'src_longitude' => $this->double(),
            'dst_longitude' => $this->double(),
            'analyzed' => $this->boolean(),
            'request_url' => $this->text(),
            'request_client_application' => $this->text(),
            'request_method' => $this->string(),
            'dst_ip_network_model' => $this->integer(),
            'src_ip_network_model' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx_events_norm_datetime', '{{%events_normalized}}', 'datetime');
        $this->createIndex('idx_events_norm_host', '{{%events_normalized}}', 'host');
        $this->createIndex('idx_events_norm_cef_vendor', '{{%events_normalized}}', 'cef_vendor');
        $this->createIndex('idx_events_norm_cef_dev_prod', '{{%events_normalized}}', 'cef_dev_prod');
        $this->createIndex('idx_events_norm_cef_name', '{{%events_normalized}}', 'cef_name');
        $this->createIndex('idx_events_norm_cef_severity', '{{%events_normalized}}', 'cef_severity');
        $this->createIndex('idx_events_norm_src_ip', '{{%events_normalized}}', 'src_ip');
        $this->createIndex('idx_events_norm_dst_ip', '{{%events_normalized}}', 'dst_ip');
        $this->createIndex('idx_events_norm_protocol', '{{%events_normalized}}', 'protocol');
        $this->createIndex('idx_events_norm_src_port', '{{%events_normalized}}', 'src_port');
        $this->createIndex('idx_events_norm_dst_port', '{{%events_normalized}}', 'dst_port');
        $this->createIndex('idx_analyzed_id', '{{%events_normalized}}', 'analyzed');
    }

    public function down()
    {
        $this->dropTable('{{%events_normalized}}');
    }
}
