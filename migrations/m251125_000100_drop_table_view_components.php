<?php

use yii\db\Migration;

class m251125_000100_drop_table_view_components extends Migration
{
    public function safeUp()
    {
        $this->dropTable('{{%view_components}}');
    }

    public function safeDown()
    {
        echo "m251125_000100_drop_table_view_components cannot be reverted.\n";

        return false;
    }
}
