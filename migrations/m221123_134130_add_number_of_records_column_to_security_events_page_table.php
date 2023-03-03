<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%security_events_page}}`.
 */
class m221123_134130_add_number_of_records_column_to_security_events_page_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%security_events_page}}', 'number_of_records', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%security_events_page}}', 'number_of_records');
    }
}
