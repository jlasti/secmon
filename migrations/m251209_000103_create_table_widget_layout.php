<?php

use yii\db\Migration;

class m251209_000103_create_table_widget_layout extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%widget_layout}}', [
            'id' => $this->primaryKey(),
            'widget_id' => $this->integer()->notNull(),
            'x' => $this->integer()->notNull(),
            'y' => $this->integer()->notNull(),
            'w' => $this->integer()->notNull(),
            'h' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx_WL_widget', '{{%widget_layout}}', 'widget_id');
        $this->addForeignKey('fk_WL_widget', '{{%widget_layout}}', 'widget_id', '{{%dashboard_widgets}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%widget_layout}}');
    }
}
