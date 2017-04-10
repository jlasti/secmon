<?php

use yii\db\Migration;

class m170312_153737_add_column_order_to_view_components_table extends Migration
{
    public function up()
    {
        $this->addColumn('view_components', 'order', $this->integer()->unsigned());
    }

    public function down()
    {
        echo "m170312_153737_add_column_order_to_view_components_table cannot be reverted.\n";

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
