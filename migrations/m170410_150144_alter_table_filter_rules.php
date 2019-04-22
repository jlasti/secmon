<?php

use yii\db\Migration;

class m170410_150144_alter_table_filter_rules extends Migration
{
    public function up()
    {
        $this->addColumn('filter_rules', 'column', $this->string());
    }

    public function down()
    {
        echo "m170410_150144_alter_table_filter_rules cannot be reverted.\n";

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
