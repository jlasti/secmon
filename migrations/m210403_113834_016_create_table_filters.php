<?php

use yii\db\Migration;

class m210403_113834_016_create_table_filters extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%filters}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'name' => $this->string(),
        ], $tableOptions);

        $this->createIndex('idx_FLT_user', '{{%filters}}', 'user_id');
        $this->addForeignKey('fk_FLT_user', '{{%filters}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%filters}}');
    }
}
