<?php

use yii\db\Migration;

class m260303_103715_add_misp_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // 1. misp_events
        $this->createTable('{{%misp_events}}', [
            'event_id'     => $this->primaryKey(),
            'event_uuid'   => $this->string(36)->null()->unique(),
            'creator_org'  => $this->text()->null(),
            'threat_level' => $this->integer()->null(),
            'analysis'     => $this->integer()->null(),
            'timestamp'    => $this->integer()->null(),
            'tags'         => $this->json()->null(),
            'created_at'   => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'is_sent'      => $this->boolean()->defaultValue(false)->notNull(),
            'sent_at'      => $this->timestamp()->null(),
        ], $tableOptions);

        $this->createIndex('idx-misp_events-is_sent', '{{%misp_events}}', 'is_sent');
        $this->createIndex('idx-misp_events-timestamp', '{{%misp_events}}', 'timestamp');

        // 2. misp_attributes
        $this->createTable('{{%misp_attributes}}', [
            'attribute_id'        => $this->primaryKey(),
            'attribute_uuid'      => $this->string(36)->null()->unique(),
            'event_id'            => $this->integer()->notNull(),
            'category'            => $this->string(100)->null(),
            'type'                => $this->string(100)->null(),
            'value'               => $this->string(191)->notNull(),
            'to_ids'              => $this->boolean()->defaultValue(true)->notNull(),
            'tags'                => $this->json()->null(),
            'timestamp'           => $this->integer()->null(),
            'comment'             => $this->text()->null(),
            'first_seen'          => $this->timestamp()->null(),
            'last_seen'           => $this->timestamp()->null(),
            'disable_correlation' => $this->boolean()->defaultValue(false)->notNull(),
            'object_relation'     => $this->string(100)->null(),
            'galaxy_clusters'     => $this->json()->null(),
        ], $tableOptions);

        $this->createIndex('idx-misp_attributes-value', '{{%misp_attributes}}', 'value');
        $this->createIndex('idx-misp_attributes-type', '{{%misp_attributes}}', 'type');
        $this->createIndex('idx-misp_attributes-event_id', '{{%misp_attributes}}', 'event_id');
        $this->createIndex('idx-misp_attributes-disable_correlation', '{{%misp_attributes}}', 'disable_correlation');

        $this->addForeignKey(
            'fk-misp_attributes-event_id',
            '{{%misp_attributes}}',
            'event_id',
            '{{%misp_events}}',
            'event_id',
            'CASCADE',
            'CASCADE'
        );

        // 3. misp_settings
        $this->createTable('{{%misp_settings}}', [
            'id'                => $this->primaryKey(),
            'sync_interval'     => $this->integer()->notNull()->defaultValue(3600),
            'ip_filters'        => $this->json()->null(),
            'misp_url'          => $this->string(255)->null(),
            'misp_api_key'      => $this->string(255)->null(),
            'sync_enabled'      => $this->boolean()->defaultValue(false),
            'export_enabled'    => $this->boolean()->defaultValue(false),
            'trigger_sync'      => $this->boolean()->defaultValue(false),
            'trigger_export'    => $this->boolean()->defaultValue(false),
            'updated_at'        => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'organization_name' => $this->string(255)->defaultValue(null),
        ], $tableOptions);

        $this->insert('{{%misp_settings}}', [
            'id'             => 1,
            'sync_interval'  => 3600,
            'ip_filters'     => json_encode([]),
            'sync_enabled'   => false,
            'export_enabled' => false,
            'trigger_sync'   => false,
            'trigger_export' => false,
        ]);

        // 4. Pridanie stĺpca do security_events (ak tabuľka existuje)
        if ($this->db->schema->getTableSchema('{{%security_events}}') !== null) {
            $this->addColumn('{{%security_events}}', 'misp_attribute_id', $this->integer()->null());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Odstránenie stĺpca zo security_events
        if ($this->db->schema->getTableSchema('{{%security_events}}') !== null) {
            $this->dropColumn('{{%security_events}}', 'misp_attribute_id');
        }

        $this->dropForeignKey('fk-misp_attributes-event_id', '{{%misp_attributes}}');
        $this->dropTable('{{%misp_attributes}}');
        $this->dropTable('{{%misp_events}}');
        $this->dropTable('{{%misp_settings}}');
    }
}