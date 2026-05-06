<?php

use yii\db\Migration;

class m251125_000101_rename_table_views_to_dashboards extends Migration
{
    private $oldTableName = '{{%views}}';
    private $newTableName = '{{%dashboards}}';

    public function up()
    {
        $this->renameTable($this->oldTableName, $this->newTableName);
    }

    public function down()
    {
        $this->renameTable($this->newTableName, $this->oldTableName);
    }
}