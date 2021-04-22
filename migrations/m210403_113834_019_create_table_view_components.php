<?php

use yii\db\Migration;

class m210403_113834_019_create_table_view_components extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%view_components}}', [
            'id' => $this->primaryKey(),
            'view_id' => $this->integer()->notNull(),
            'filter_id' => $this->integer(),
            'config' => $this->text(),
            'order' => $this->integer(),
            'data_type' => $this->text(),
            'data_param' => $this->text(),
        ], $tableOptions);

        $this->createIndex('idx_VC_view', '{{%view_components}}', 'view_id');
        $this->createIndex('idx_VC_filter', '{{%view_components}}', 'filter_id');
        $this->addForeignKey('fk_VC_view', '{{%view_components}}', 'view_id', '{{%views}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_VC_filter', '{{%view_components}}', 'filter_id', '{{%filters}}', 'id', 'SET NULL', 'SET NULL');
    }

    public function down()
    {
        $this->dropTable('{{%view_components}}');
    }
}
