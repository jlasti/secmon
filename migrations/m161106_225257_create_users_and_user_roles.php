<?php

use yii\db\Migration;

class m161106_225257_create_users_and_user_roles extends Migration
{
	public function up()
	{
		$this->createRolesTable();
		$this->createPermissionsTable();
		$this->createUsersTable();
	}

	public function down()
	{
		echo "m161106_225257_create_users_and_user_roles cannot be reverted.\n";

		return false;
	}

	private function createRolesTable()
	{
		$this->createTable('roles', [
			'id' => $this->primaryKey()->unsigned(),
			'name' => $this->string(),
			'slug' => $this->string(),
		], 'ENGINE=InnoDB');
	}

	private function createPermissionsTable()
	{
		$this->createTable('permissions', [
			'id' => $this->primaryKey()->unsigned(),
			'name' => $this->string(),
			'slug' => $this->string(),
		], 'ENGINE=InnoDB');

		$this->createTable('rel_role_permission', [
			'role_id' => $this->integer()->unsigned(),
			'permission_id' => $this->integer()->unsigned(),
		], 'ENGINE=InnoDB');

		$this->createIndex('idx_RP_role_id', 'rel_role_permission', 'role_id');
		$this->createIndex('idx_RP_permission_id', 'rel_role_permission', 'permission_id');

		$this->addForeignKey('fk_RP_role_id', 'rel_role_permission', 'role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_RP_permission_id', 'rel_role_permission', 'permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');
	}

	private function createUsersTable()
	{
		$this->createTable('users', [
			'id' => $this->primaryKey()->unsigned(),
			'first_name' => $this->string(),
			'last_name' => $this->string(),
			'username' => $this->string(),
			'password' => $this->string(),
			'email' => $this->string(),
			'auth_key' => $this->string(),
		], 'ENGINE=InnoDB');

		$this->createTable('rel_user_role', [
			'user_id' => $this->integer()->unsigned(),
			'role_id' => $this->integer()->unsigned(),
		], 'ENGINE=InnoDB');

		$this->createIndex('idx_UR_user_id', 'rel_user_role', 'user_id');
		$this->createIndex('idx_UR_role_id', 'rel_user_role', 'role_id');

		$this->addForeignKey('fk_UR_user_id', 'rel_user_role', 'user_id', 'users', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_UR_role_id', 'rel_user_role', 'role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
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
