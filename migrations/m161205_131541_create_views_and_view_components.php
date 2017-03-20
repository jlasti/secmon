<?php

use yii\db\Migration;

class m161205_131541_create_views_and_view_components extends Migration
{
    public function up()
    {
		$this->createTable('views', [
			'id' => $this->primaryKey()->unsigned(),
			'name' => $this->string(),
			'user_id' => $this->integer()->unsigned(),
			'active' => $this->boolean(),
		], 'ENGINE=InnoDB');

		$this->createIndex('idx_VIEW_user', 'views', 'user_id');

		$this->addForeignKey('fk_VIEW_user', 'views', 'user_id', 'users', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('view_components', [
			'id' => $this->primaryKey()->unsigned(),
			'view_id' => $this->integer()->unsigned()->notNull(),
			'filter_id' => $this->integer()->unsigned()->null(),
			'column' => $this->smallInteger()->unsigned(),
			'row' => $this->smallInteger()->unsigned(),
			'width' => $this->smallInteger()->unsigned(),
			'height' => $this->smallInteger()->unsigned(),
		], 'ENGINE=InnoDB');

		$this->createIndex('idx_VC_view', 'view_components', 'view_id');
		$this->createIndex('idx_VC_filter', 'view_components', 'filter_id');

		$this->addForeignKey('fk_VC_view', 'view_components', 'view_id', 'views', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_VC_filter', 'view_components', 'filter_id', 'filters', 'id', 'SET NULL', 'SET NULL');
    }

    public function down()
    {
        echo "m161205_131541_create_views_and_view_components cannot be reverted.\n";

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
