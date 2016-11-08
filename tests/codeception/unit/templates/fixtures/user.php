<?php

/* @var $faker \Faker\Generator */

return [
	'first_name' => $faker->firstName,
	'last_name' => $faker->lastName,
	'username' => $faker->userName,
	'password' => Yii::$app->security->generatePasswordHash('password_' . $index),
	'email' => $faker->email,
	'auth_key' => Yii::$app->security->generateRandomString(),
];