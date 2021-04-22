<?php

use yii\db\Migration;

class m210403_113834_011_create_table_views extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%views}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'user_id' => $this->integer(),
            'active' => $this->boolean(),
            'config' => $this->text(),
            'refresh_time' => $this->text(),
        ], $tableOptions);

        $this->createIndex('idx_VIEW_user', '{{%views}}', 'user_id');
        $this->addForeignKey('fk_VIEW_user', '{{%views}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%views}}');
    }
}
