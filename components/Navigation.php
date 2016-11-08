<?php

namespace app\components;

use Yii;

class Navigation extends \yii\base\Component
{
	private $_items;

	public function init()
	{
		$this->_items = [
	        ['label' => 'Users', 'url' => ['/user'], 'visible' => Yii::$app->user->identity->can('create_users')],
	        ['label' => 'Roles', 'url' => ['/role'], 'visible' => Yii::$app->user->identity->can('create_users')],
	        ['label' => 'Eventy', 'url' => ['/event']],
	        ['label' => 'Typy eventov', 'url' => ['/event-type'], 'visible' => Yii::$app->user->identity->can('create_users')],
	    ];
	}

	public function getItems()
	{
		return $this->_items;
	}
}