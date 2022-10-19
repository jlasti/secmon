<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%security_events_page}}`.
 */
class m221019_090114_drop_auto_refresh_column_from_security_events_page_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%security_events_page}}', 'auto_refresh');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%security_events_page}}', 'auto_refresh', $this->boolean());
    }
}
