<?php

use yii\db\Migration;

class m210403_113834_010_create_table_users extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(),
            'last_name' => $this->string(),
            'username' => $this->string(),
            'password' => $this->string(),
            'email' => $this->string(),
            'auth_key' => $this->string(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%users}}');
    }
}
