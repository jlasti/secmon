<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%security_events}}`.
 */
class m231228_190055_add_source_cti_id_and_destination_cti_id_columns_to_security_events_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('security_events', 'source_cti_id', $this->integer());
        $this->addColumn('security_events', 'destination_cti_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('security_events', 'source_cti_id');
        $this->dropColumn('security_events', 'destination_cti_id');
    }
}
