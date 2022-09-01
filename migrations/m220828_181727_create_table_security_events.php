<?php

use yii\db\Migration;

/**
 * Class m220828_181727_create_table_security_events
 */
class m220828_181727_create_table_security_events extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%security_events}}', [
          'id' => $this->bigPrimaryKey(),
          'datetime' => $this->timestamp(),
          'type' => $this->string(),
          'cef_version' => $this->string(31)->notNull(),
          'cef_severity' => $this->integer()->notNull(),
          'cef_event_class_id' => $this->string(1023)->notNull(),
          'cef_device_product' => $this->string(63)->notNull(),
          'cef_vendor' => $this->string(63)->notNull(),
          'cef_device_version' => $this->string(31)->notNull(),
          'cef_name' => $this->string(512)->notNull(),
          'device_action' => $this->string(63),
          'application_protocol' => $this->string(31),
          'device_custom_ipv6_address1' => $this->string(),
          'device_custom_ipv6_address1_label' => $this->string(1023),
          'device_custom_ipv6_address2' => $this->string(),
          'device_custom_ipv6_address2_label' => $this->string(1023),
          'device_custom_ipv6_address3' => $this->string(),
          'device_custom_ipv6_address3_label' => $this->string(1023),
          'device_custom_ipv6_address4' => $this->string(),
          'device_custom_ipv6_address4_label' => $this->string(1023),
          'device_event_category' => $this->string(1023),
          'device_custom_floating_point1' => $this->double(),
          'device_custom_floating_point1_label' => $this->string(1023),
          'device_custom_floating_point2' => $this->double(),
          'device_custom_floating_point2_label' => $this->string(1023),
          'device_custom_floating_point3' => $this->double(),
          'device_custom_floating_point3_label' => $this->string(1023),
          'device_custom_floating_point4' => $this->double(),
          'device_custom_floating_point4_label' => $this->string(1023),
          'device_custom_number1' => $this->biginteger(),
          'device_custom_number1_label' => $this->string(1023),
          'device_custom_number2' => $this->biginteger(),
          'device_custom_number2_label' => $this->string(1023),
          'device_custom_number3' => $this->biginteger(),
          'device_custom_number3_label' => $this->string(1023),
          'baseEventCount' => $this->integer(),
          'device_custom_string1' => $this->string(4000),
          'device_custom_string1_label' => $this->string(1023), 
          'device_custom_string2' => $this->string(4000),
          'device_custom_string2_label' => $this->string(1023), 
          'device_custom_string3' => $this->string(4000),
          'device_custom_string3_label' => $this->string(1023), 
          'device_custom_string4' => $this->string(4000),
          'device_custom_string4_label' => $this->string(1023), 
          'device_custom_string5' => $this->string(4000),
          'device_custom_string5_label' => $this->string(1023), 
          'device_custom_string6' => $this->string(4000),
          'device_custom_string6_label' => $this->string(1023),
          'device_custom_date1' => $this->timestamp(),
          'device_custom_date1_label' => $this->string(1023),
          'device_custom_date2' => $this->timestamp(),
          'device_custom_date2_label' => $this->string(1023),
          'device_direction' => $this->integer(),
          'device_dns_domain' => $this->string(255),
          'device_external_id' => $this->string(255),
          'device_facility' => $this->string(1023),
          'device_inbound_interface' => $this->string(128),
          'device_nt_domain' => $this->string(255),
          'device_outbound_interface' => $this->string(128),
          'device_payload_id' => $this->string(128),
          'device_process_name' => $this->string(1023),
          'device_translated_address' =>$this->string(),
          'device_time_zone' => $this->string(255),
          'device_address' => $this->string(),
          'device_host_name' => $this->string(100),
          'device_mac_address' => $this->string(),
          'device_process_id' => $this->integer(),
          'destination_host_name' => $this->string(1023),
          'destination_mac_address' =>$this->string(),
          'destination_nt_domain' => $this->string(255),
          'destination_dns_domain' => $this->string(255),
          'destination_service_name' => $this->string(1023),
          'destination_translated_address' => $this->string(),
          'destination_translated_port' => $this->integer(),
          'destination_process_id' => $this->integer(),
          'destination_user_privileges' => $this->string(1023),
          'destination_process_name' => $this->string(1023),
          'destination_port'  => $this->integer(),
          'destination_address' => $this->string(),
          'destination_user_id' => $this->string(1023),
          'destination_user_name' => $this->string(1023),
          'destination_group_id' => $this->string(1023),
          'destination_group_name' => $this->string(1023),
          'end_time' => $this->timestamp(),
          'external_id' => $this->string(40),
          'file_create_time' => $this->timestamp(),
          'file_hash' => $this->string(255),
          'file_id' => $this->string(1023),
          'file_modification_time' => $this->timestamp(),
          'file_name' => $this->string(1023),
          'file_path' => $this->string(1023),
          'file_permission' => $this->string(1023),
          'file_size' => $this->integer(),
          'file_type' => $this->string(1023),
          'old_file_create_time' => $this->timestamp(),
          'old_file_hash' => $this->string(255),
          'old_file_id' => $this->string(1023),
          'old_file_modification_time' => $this->timestamp(),
          'old_file_name' => $this->string(1023),
          'old_file_path' => $this->string(1023),
          'old_file_permission' => $this->string(1023),
          'old_file_size' => $this->integer(),
          'old_file_type' => $this->string(1023),
          'flex_date1' => $this->timestamp(),
          'flex_date1_label' => $this->string(128),
          'flex_string1' => $this->string(1023),
          'flex_string1_label' => $this->string(128),
          'flex_string2' => $this->string(1023),
          'flex_string2_label' => $this->string(128),
          'bytes_in' => $this->integer(),
          'bytes_out' => $this->integer(),
          'message' => $this->string(1023),
          'event_outcome' => $this->string(63),
          'transport_protocol' => $this->string(31),
          'reason' => $this->string(1023),
          'request_url' => $this->string(1023),
          'request_client_application' => $this->string(1023),
          'request_context' => $this->string(2048),
          'request_cookies' => $this->string(1023),
          'request_method' => $this->string(1023),
          'device_receipt_time' => $this->timestamp(),
          'source_host_name' => $this->string(1023),
          'source_mac_address' =>$this->string(),
          'source_nt_domain' => $this->string(255),
          'source_dns_domain' => $this->string(255),
          'source_service_name' => $this->string(1023),
          'source_translated_address' => $this->string(),
          'source_translated_port' => $this->integer(),
          'source_process_id' => $this->integer(),
          'source_user_privileges' => $this->string(1023),
          'source_process_name' => $this->string(1023),
          'source_port'  => $this->integer(),
          'source_address' => $this->string(),
          'source_user_id' => $this->string(1023),
          'source_user_name' => $this->string(1023),
          'source_group_id' => $this->string(1023),
          'source_group_name' => $this->string(1023),
          'start_time' => $this->timestamp(),
          'agent_translated_zone_key' => $this->biginteger(),
          'agent_zone_key' => $this->biginteger(),
          'customer_key' => $this->biginteger(),
          'destination_translated_zone_key' => $this->biginteger(),
          'destination_zone_key' => $this->biginteger(),
          'device_translated_zone_key' => $this->biginteger(),
          'device_zone_key' => $this->biginteger(),
          'source_translated_zone_key' => $this->biginteger(),
          'source_zone_key' => $this->biginteger(),
          'reported_duration' => $this->biginteger(),
          'reported_resource_group_name' => $this->string(128),
          'reported_resource_id' => $this->string(256),
          'reported_resource_name' => $this->string(64),
          'reported_resource_type' => $this->string(64),
          'framework_name' => $this->string(256),
          'threat_actor' => $this->string(40),
          'threat_attack_id' => $this->string(32),
          'attack_type' => $this->string(64), //not from CEF
          'source_country' => $this->string(64), //not from CEF
          'source_city' => $this->string(64), //not from CEF
          'destination_country' => $this->string(64), //not from CEF
          'destination_city' => $this->string(64), //not from CEF
          'source_geo_longitude' => $this->double(),
          'source_geo_latitude' => $this->double(),
          'destination_geo_longitude' => $this->double(),
          'destination_geo_latitude' => $this->double(),
          'destination_ip_network_model' => $this->integer(), //not from CEF
          'source_ip_network_model' => $this->integer(), //not from CEF
          'source_code' => $this->string(), //not from CEF
          'destination_code' => $this->string(), //not from CEF
          'parent_events' => $this->text(), //not from CEF
          'analyzed' => $this->boolean(), //not from CEF
          'cef_extensions' => $this->text(), //asi toto bude useless, keďže tu máme už všetky CEF polia
          'raw_event' => $this->text(),
        ], $tableOptions);

        $this->createIndex('idx_security_events_datetime', '{{%security_events}}', 'datetime');
        $this->createIndex('idx_security_events_device_host_name', '{{%security_events}}', 'device_host_name');
        $this->createIndex('idx_security_events_type', '{{%security_events}}', 'type');
        $this->createIndex('idx_security_events_cef_vendor', '{{%security_events}}', 'cef_vendor');
        $this->createIndex('idx_security_events_cef_device_product', '{{%security_events}}', 'cef_device_product');
        $this->createIndex('idx_security_events_cef_name', '{{%security_events}}', 'cef_name');
        $this->createIndex('idx_security_events_cef_severity', '{{%security_events}}', 'cef_severity');
        $this->createIndex('idx_security_events_source_address', '{{%security_events}}', 'source_address');
        $this->createIndex('idx_security_events_destination_address', '{{%security_events}}', 'destination_address');
        $this->createIndex('idx_security_events_application_protocol', '{{%security_events}}', 'application_protocol');
        $this->createIndex('idx_security_events_source_port', '{{%security_events}}', 'source_port');
        $this->createIndex('idx_security_events_destination_port', '{{%security_events}}', 'destination_port');
        //$this->createIndex('idx_analyzed_events_id', '{{%security_events}}', 'analyzed');
    }

    public function down()
    {
        $this->dropTable('{{%security_events}}');
    }
}