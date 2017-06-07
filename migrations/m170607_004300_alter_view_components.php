<?php

use yii\db\Migration;

class m170607_004300_alter_view_components extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn('view_components', 'data_type', $this->text());
        $this->addColumn('view_components', 'data_param', $this->text());
    }

    public function safeDown()
    {
        $this->dropColumn('view_components', 'data_type');
        $this->dropColumn('view_components', 'data_param');
    }
}
