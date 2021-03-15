<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%events_normalized}}`.
 */
class m210315_094919_add_requestURL_column_requestClientApplication_column_to_events_normalized_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%events_normalized}}', 'request_url', $this->text());
        $this->addColumn('{{%events_normalized}}', 'request_client_application', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%events_normalized}}', 'request_url');
        $this->dropColumn('{{%events_normalized}}', 'request_client_application');
    }
}
