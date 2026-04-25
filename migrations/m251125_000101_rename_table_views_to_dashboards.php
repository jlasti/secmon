<?php

use yii\db\Migration;

/**
 * Handles renaming of table `{{%views}}` to `{{%dashboards}}`.
 */
class m251125_000101_rename_table_views_to_dashboards extends Migration
{
    private $oldTableName = '{{%views}}';
    private $newTableName = '{{%dashboards}}';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->renameTable($this->oldTableName, $this->newTableName);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        // Revert: rename the new table name back to the old one
        $this->renameTable($this->newTableName, $this->oldTableName);
    }
}