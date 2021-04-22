<?php

use yii\db\Migration;

class m210403_113834_007_create_table_permissions extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%permissions}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'slug' => $this->string(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%permissions}}');
    }
}
