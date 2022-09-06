<?php

use yii\db\Migration;

/**
 * Class m220906_103925_create_table_time_filter_rules
 */
class m220906_103925_create_table_time_filter_rules extends Migration
{
    public function up()
    {
        $this->createTable('{{%time_filter_rules}}', [
            'id' => $this->primaryKey(),
            'time_filter_id' => $this->integer(),
            'type' => 'date',
            'value' => $this->text(),
            'operator' => $this->string(255),
            'logic_operator' => $this->string(255),
            'position' => $this->integer(),
            'column' => 'datetime',
        ]);

        $this->addForeignKey('fk-time_filter_id', '{{%time_filter_rules}}', 'time_filter_id', '{{%time_filters}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%time_filter_rules}}');
    }
}
