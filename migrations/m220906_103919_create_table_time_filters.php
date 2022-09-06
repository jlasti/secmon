<?php

use yii\db\Migration;

/**
 * Class m220906_103919_create_table_time_filters
 */
class m220906_103919_create_table_time_filters extends Migration
{
    public function up()
    {
        $this->createTable('{{%time_filters}}', [
            'id' => $this->primaryKey(),
            'filter_type' => $this->boolean(),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%time_filters}}');
    }
}
