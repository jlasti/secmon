<?php

use yii\db\Migration;

/**
 * Class m220906_103932_create_table_security_events_page
 */
class m220906_103932_create_table_security_events_page extends Migration
{
    public function up()
    {
        $this->createTable('{{%security_events_page}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'filter_id' => $this->integer(),
            'time_filter_id' => $this->integer(),
            'refresh_time' => $this->text(),
            'columns' => $this->text(),
        ]);

        $this->addForeignKey('fk-security_events_page-user_id', '{{%security_events_page}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-security_events_page-filter_id', '{{%security_events_page}}', 'filter_id', '{{%filters}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-security_events_page-time_filter_id', '{{%security_events_page}}', 'time_filter_id', '{{%time_filters}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%security_events_page}}');
    }
}
