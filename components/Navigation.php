<?php

namespace app\components;

use Yii;

class Navigation extends \yii\base\Component
{
	private $_items;

	public function init()
	{
		$this->_items = [
	        ['label' => 'Users', 'url' => ['/user'], 'visible' => Yii::$app->user->identity->can('create_users'), 'active' => 'user'],
	        ['label' => 'Roles', 'url' => ['/role'], 'visible' => Yii::$app->user->identity->can('create_users'), 'active' => 'role'],
	        ['label' => 'Eventy', 'url' => ['/event'], 'visible' => true, 'active' => 'event'],
	        ['label' => 'Typy eventov', 'url' => ['/event-type'], 'visible' => Yii::$app->user->identity->can('create_users'), 'active' => 'event-type'],
	    ];
	}

	public function getItems()
	{
		return $this->_items;
	}
}