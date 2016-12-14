<?php

use yii\db\Migration;

class m161212_195149_add_columns_to_filter_rules_table extends Migration
{
    public function up()
    {
        $this->addColumn('filter_rules', 'logic_operator', $this->string()->null());
        $this->addColumn('filter_rules', 'position', $this->integer()->unsigned());
    }

    public function down()
    {
        echo "m161212_195149_add_columns_to_filter_rules_table cannot be reverted.\n";

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
