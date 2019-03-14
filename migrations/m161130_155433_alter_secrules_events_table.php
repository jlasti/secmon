<?php

use yii\db\Migration;

class m161130_155433_alter_secrules_events_table extends Migration
{
    public function up()
    {        
        $this->addColumn('sec_rules', 'state', $this->boolean());
        $this->renameColumn('sec_rules', 'content', 'link');

    }

    public function down()
    {
        echo "m161130_155433_alter_secrules_events_table cannot be reverted.\n";

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
