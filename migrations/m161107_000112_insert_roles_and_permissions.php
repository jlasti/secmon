<?php

use yii\db\Migration;

class m161107_000112_insert_roles_and_permissions extends Migration
{
	private $_roles = [
		1 => 'User',
		100 => 'Admin',
	];

	private $_permissions = [
		1 => ['slug' => 'create_users', 'name' => 'Create users'],
		2 => ['slug' => 'update_users', 'name' => 'Update users'],
		3 => ['slug' => 'delete_users', 'name' => 'Delete users'],
		4 => ['slug' => 'view_users', 'name' => 'View users'],
	];

	private $_map = [
		1 => [4],
		100 => [1, 2, 3, 4],
	];

    public function up()
    {
		foreach($this->_roles as $id => $role)
		{
			$this->insert('roles', [
				'id' => $id,
				'name' => $role,
				'slug' => strtolower($role),
			]);
		}

		foreach($this->_permissions as $id => $permission)
		{
			$this->insert('permissions', [
				'id' => $id,
				'name' => $permission['name'],
				'slug' => $permission['slug'],
			]);
		}

		foreach($this->_map as $role => $permissions)
		{
			foreach($permissions as $permission)
			{
				$this->insert('rel_role_permission', [
					'role_id' => $role,
					'permission_id' => $permission,
				]);
			}
		}
    }

    public function down()
    {
        echo "m161107_000112_insert_roles_and_permissions cannot be reverted.\n";

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
