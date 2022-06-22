<?php

use yii\db\Migration;

class m210403_113834_009_create_table_sec_rules extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sec_rules}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'link' => $this->string(),
            'state' => $this->boolean(),
            'type' => $this->string(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%sec_rules}}');
    }
}
