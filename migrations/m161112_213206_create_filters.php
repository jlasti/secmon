<?php

use yii\db\Migration;

class m161112_213206_create_filters extends Migration
{
    public function up()
    {
		$this->createTable('filters', [
			'id' => $this->primaryKey()->unsigned(),
			'user_id' => $this->integer()->unsigned(),
			'name' => $this->string(),
		]);

		$this->createIndex('idx_FLT_user', 'filters', 'user_id');

		$this->addForeignKey('fk_FLT_user', 'filters', 'user_id', 'users', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('filter_rules', [
			'id' => $this->primaryKey()->unsigned(),
			'filter_id' => $this->integer()->unsigned(),
			'type' => $this->string(),
			'value' => $this->text(),
			'operator' => $this->string(),
		]);

		$this->createIndex('idx_FR_filter', 'filter_rules', 'filter_id');

		$this->addForeignKey('fk_FR_filter', 'filter_rules', 'filter_id', 'filters', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        echo "m161112_213206_create_filters cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
