<?php

use yii\db\Migration;

class m170326_200254_update_events_to_cef extends Migration
{
    public function up()
    {
		$this->dropForeignKey('fk_events_type_id', 'events');
		$this->dropIndex('idx_events_type_id', 'events');
		$this->dropTable('events');

		$this->createTable('events_correlated', [
			'id' => $this->bigPrimaryKey()->unsigned(),
			'datetime' => $this->dateTime(),
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
		]);

		$this->createIndex('idx_events_corr_datetime', 'events_correlated', 'datetime');
		$this->createIndex('idx_events_corr_host', 'events_correlated', 'host');
		$this->createIndex('idx_events_corr_cef_vendor', 'events_correlated', 'cef_vendor');
		$this->createIndex('idx_events_corr_cef_dev_prod', 'events_correlated', 'cef_dev_prod');
		$this->createIndex('idx_events_corr_cef_name', 'events_correlated', 'cef_name');
		$this->createIndex('idx_events_corr_cef_severity', 'events_correlated', 'cef_severity');

		$this->createTable('events_normalized', [
			'id' => $this->bigPrimaryKey()->unsigned(),
			'datetime' => $this->dateTime(),
			'host' => $this->string(),
			'cef_version' => $this->string()->notNull(),
			'cef_vendor' => $this->string()->notNull(),
			'cef_dev_prod' => $this->string()->notNull(),
			'cef_dev_version' => $this->string()->notNull(),
			'cef_event_class_id' => $this->integer()->notNull(),
			'cef_name' => $this->string()->notNull(),
			'cef_severity' => $this->integer()->notNull(),

			'src_ip' => 'varchar(255)',
			'dst_ip' => 'varchar(255)',
			'src_port' => $this->smallInteger()->unsigned(),
			'dst_port' => $this->smallInteger()->unsigned(),
			'protocol' => $this->string(),
			'src_mac' => $this->string(),
			'dst_mac' => $this->string(),
			'extensions' => $this->text(),
			'raw' => $this->text(),
		]);

		$this->createIndex('idx_events_norm_datetime', 'events_normalized', 'datetime');
		$this->createIndex('idx_events_norm_host', 'events_normalized', 'host');
		$this->createIndex('idx_events_norm_cef_vendor', 'events_normalized', 'cef_vendor');
		$this->createIndex('idx_events_norm_cef_dev_prod', 'events_normalized', 'cef_dev_prod');
		$this->createIndex('idx_events_norm_cef_name', 'events_normalized', 'cef_name');
		$this->createIndex('idx_events_norm_cef_severity', 'events_normalized', 'cef_severity');

		$this->createIndex('idx_events_norm_src_ip', 'events_normalized', 'src_ip');
		$this->createIndex('idx_events_norm_dst_ip', 'events_normalized', 'dst_ip');
		$this->createIndex('idx_events_norm_src_port', 'events_normalized', 'src_port');
		$this->createIndex('idx_events_norm_dst_port', 'events_normalized', 'dst_port');
		$this->createIndex('idx_events_norm_protocol', 'events_normalized', 'protocol');
    }

    public function down()
    {
        echo "m170326_200254_update_events_to_cef cannot be reverted.\n";

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
