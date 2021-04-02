<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%events_normalized}}`.
 */
class m210318_165423_add_requestMethod_column_to_events_normalized_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%events_normalized}}', 'request_method', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%events_normalized}}', 'request_method');
    }
}
