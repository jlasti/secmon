<?php

use yii\db\Migration;

class m210403_113834_018_create_table_rel_user_role extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%rel_user_role}}', [
            'user_id' => $this->integer(),
            'role_id' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx_UR_user_id', '{{%rel_user_role}}', 'user_id');
        $this->createIndex('idx_UR_role_id', '{{%rel_user_role}}', 'role_id');
        $this->addForeignKey('fk_UR_user_id', '{{%rel_user_role}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_UR_role_id', '{{%rel_user_role}}', 'role_id', '{{%roles}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%rel_user_role}}');
    }
}
