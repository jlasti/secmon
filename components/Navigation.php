<?php

namespace app\components;

use Yii;

class Navigation extends \yii\base\Component
{
	private $_items;

	public function init()
	{
		$this->_items = [
	        ['label' => 'Events', 'url' => ['/event'], 'visible' => true, 'active' => 'event'],
	        ['label' => 'Filters', 'url' => ['/filter'], 'visible' => true, 'active' => 'filter'],
	        ['label' => 'Event types', 'url' => ['/event-type'], 'visible' => Yii::$app->user->identity->can('create_users'), 'active' => 'event-type'],
	        ['label' => 'Sec Rules', 'url' => ['/sec-rule'], 'visible' => Yii::$app->user->identity->can('create_users'), 'active' => 'sec-rule'],
	        ['label' => 'Users', 'url' => ['/user'], 'visible' => Yii::$app->user->identity->can('create_users'), 'active' => 'user'],
	        ['label' => 'Roles', 'url' => ['/role'], 'visible' => Yii::$app->user->identity->can('create_users'), 'active' => 'role'],
	    ];
	}

	public function getItems()
	{
		return $this->_items;
	}
}