<?php

use yii\db\Migration;

class m210403_113834_017_create_table_rel_role_permission extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%rel_role_permission}}', [
            'role_id' => $this->integer(),
            'permission_id' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx_RP_role_id', '{{%rel_role_permission}}', 'role_id');
        $this->createIndex('idx_RP_permission_id', '{{%rel_role_permission}}', 'permission_id');
        $this->addForeignKey('fk_RP_role_id', '{{%rel_role_permission}}', 'role_id', '{{%roles}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_RP_permission_id', '{{%rel_role_permission}}', 'permission_id', '{{%permissions}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%rel_role_permission}}');
    }
}
