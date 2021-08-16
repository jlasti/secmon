<?php

use yii\db\Migration;

/**
 * Class m210429_091033_network_model
 */
class m210429_091033_create_table_network_model extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%network_model}}', [
            'id' => $this->primaryKey(),
            'ip_address' => $this->string()->notNUll(),
            'mac_address' => $this->string(),
            'description' => $this->string(),
            'hostname' => $this->string(),
            'operation_system' => $this->string(),
            'open_ports' => $this->string(),
            'ports' => $this->string(),
            'services' => $this->string(),
            'vulnerabilities' => $this->string(),
            'criticality' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('fk_src_network_model', '{{%events_normalized}}', 'src_ip_network_model', '{{%network_model}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_dst_network_model', '{{%events_normalized}}', 'dst_ip_network_model', '{{%network_model}}', 'id', 'CASCADE', 'CASCADE');
    }


    public function down()
    {
        $this->dropTable('{{%network_model}}');

        return false;
    }
    
}
