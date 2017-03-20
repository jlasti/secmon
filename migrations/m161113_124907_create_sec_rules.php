<?php

use yii\db\Migration;

class m161113_124907_create_sec_rules extends Migration
{
    public function up()
    {
        $this->createSecRulesTable();
    }

    private function createSecRulesTable()
    {
        $this->createTable('sec_rules', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(),
            'content' => $this->string(),
        ], 'ENGINE=InnoDB');
    }

    public function down()
    {
        $this->dropTable('sec_rules');

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}