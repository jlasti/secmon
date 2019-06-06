<?php

$users = \app\models\User::find()->select('id')->asArray()->all();
$roles = \app\models\Role::find()->select('id')->asArray()->all();

$data = [];

foreach($users as $user)
{
	shuffle($roles);

	$data[] = [
		'user_id' => $user['id'],
		//'role_id' => reset($roles)['id'],
		'role_id' => 100,
	];
}

return $data;
