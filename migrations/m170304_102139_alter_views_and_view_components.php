<?php

use yii\db\Migration;

class m170304_102139_alter_views_and_view_components extends Migration
{
    public function up()
    {
        $this->addColumn('views', 'config', $this->text());
        $this->addColumn('view_components', 'config', $this->text());


        $this->dropColumn('view_components', 'column');
        $this->dropColumn('view_components', 'row');
        $this->dropColumn('view_components', 'width');
        $this->dropColumn('view_components', 'height');
        $this->dropColumn('view_components', 'type');
    }

    public function down()
    {
        echo "m170304_102139_alter_views_and_view_components cannot be reverted.\n";

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
