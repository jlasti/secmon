<?php

use yii\db\Migration;

/**
 * Class m220830_215114_create_table_analyzed_security_events_list
 */
class m220830_215114_create_table_analyzed_security_events_list extends Migration
{
    public function up()
    {
        $this->createTable('{{%analyzed_security_events_list}}', [
            'id' => $this->primaryKey(),
            'events_analyzed_iteration' => $this->integer(),
            'security_events_id' => $this->integer(),
            'analyzed_security_events_id' => $this->integer(),
        ]);

        $this->createIndex('idx_analyzed_security_events_list_id', '{{%analyzed_security_events_list}}', 'id');
        $this->addForeignKey('fk_analyzed_security_events_id', '{{%analyzed_security_events_list}}', 'analyzed_security_events_id', '{{%security_events}}', 'id', 'SET NULL', 'SET NULL');
    }

    public function down()
    {
        $this->dropTable('{{%analyzed_security_events_list}}');
    }
}
