<?php

use yii\db\Migration;

/**
 * Class m210429_093420_interface
 */
class m210429_093420_create_table_interface extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%interface}}', [
            'id' => $this->primaryKey(),
            'network_model_id' => $this->integer()->notNull(),
            'ip_address' => $this->string()->notNUll(),
            'mac_address' => $this->string(),
            'name' => $this->string(),
        ], $tableOptions);

        $this->addForeignKey('fk_network_model_id ', '{{%interface}}', '(network_model_id) ', '{{%network_model}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        echo "m210429_093420_interface cannot be reverted.\n";

        return false;
    }
}
