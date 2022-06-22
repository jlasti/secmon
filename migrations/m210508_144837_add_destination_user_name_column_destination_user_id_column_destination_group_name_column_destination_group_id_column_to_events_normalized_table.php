<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%events_normalized}}`.
 */
class m210508_144837_add_destination_user_name_column_destination_user_id_column_destination_group_name_column_destination_group_id_column_to_events_normalized_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%events_normalized}}', 'destination_user_name', $this->text());
        $this->addColumn('{{%events_normalized}}', 'destination_user_id', $this->integer());
        $this->addColumn('{{%events_normalized}}', 'destination_group_name', $this->text());
        $this->addColumn('{{%events_normalized}}', 'destination_group_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%events_normalized}}', 'destination_user_name');
        $this->dropColumn('{{%events_normalized}}', 'destination_user_id');
        $this->dropColumn('{{%events_normalized}}', 'destination_group_name');
        $this->dropColumn('{{%events_normalized}}', 'destination_group_id');
    }
}
