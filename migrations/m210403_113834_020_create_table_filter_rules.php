<?php

use yii\db\Migration;

class m210403_113834_020_create_table_filter_rules extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%filter_rules}}', [
            'id' => $this->primaryKey(),
            'filter_id' => $this->integer(),
            'type' => $this->string(),
            'value' => $this->text(),
            'operator' => $this->string(),
            'logic_operator' => $this->string(),
            'position' => $this->integer(),
            'column' => $this->string(),
        ], $tableOptions);

        $this->createIndex('idx_FR_filter', '{{%filter_rules}}', 'filter_id');
        $this->addForeignKey('fk_FR_filter', '{{%filter_rules}}', 'filter_id', '{{%filters}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%filter_rules}}');
    }
}
