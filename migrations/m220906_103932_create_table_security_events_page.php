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
            'time_filter_type' => $this->string(),
            'auto_refresh' => $this->boolean()->defaultValue(true),
            'refresh_time' => $this->text(),
            'data_columns' => $this->text(),
        ]);

        $this->addForeignKey('fk-security_events_page-user_id', '{{%security_events_page}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'NO ACTION');
        $this->addForeignKey('fk-security_events_page-filter_id', '{{%security_events_page}}', 'filter_id', '{{%filters}}', 'id', 'SET DEFAULT', 'SET DEFAULT');
        $this->addForeignKey('fk-security_events_page-time_filter_id', '{{%security_events_page}}', 'time_filter_id', '{{%filters}}', 'id', 'SET DEFAULT', 'SET DEFAULT');
    }

    public function down()
    {
        $this->dropTable('{{%security_events_page}}');
    }
}
