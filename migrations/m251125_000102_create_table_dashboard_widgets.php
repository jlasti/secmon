<?php

use yii\db\Migration;

class m251125_000102_create_table_dashboard_widgets extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%dashboard_widgets}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'chart_type' => $this->string(100),
            'timeframe' => $this->string(100),
            'config' => $this->json(),
            'dashboard_id' => $this->integer()->notNull(),
            'filter_id' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx_DW_dashboard', '{{%dashboard_widgets}}', 'dashboard_id');
        $this->createIndex('idx_DW_filter', '{{%dashboard_widgets}}', 'filter_id');
        $this->addForeignKey('fk_DW_dashboard', '{{%dashboard_widgets}}', 'dashboard_id', '{{%dashboards}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_DW_filter', '{{%dashboard_widgets}}', 'filter_id', '{{%filters}}', 'id', 'SET NULL', 'SET NULL');
    }

    public function down()
    {
        $this->dropTable('{{%dashboard_widgets}}');
    }
}
