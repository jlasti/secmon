<?php

namespace app\components;

use Yii;

class Navigation extends \yii\base\Component
{
	private $_items;

	public function init()
	{
<<<<<<< HEAD
		$this->_items = [
	        ['label' => 'Users', 'url' => ['/user'], 'visible' => Yii::$app->user->identity->can('create_users'), 'active' => 'user'],
	        ['label' => 'Roles', 'url' => ['/role'], 'visible' => Yii::$app->user->identity->can('create_users'), 'active' => 'role'],
	        ['label' => 'Eventy', 'url' => ['/event'], 'visible' => true, 'active' => 'event'],
	        ['label' => 'Typy eventov', 'url' => ['/event-type'], 'visible' => Yii::$app->user->identity->can('create_users'), 'active' => 'event-type'],
=======
		$this->_items = 
		[
	        [
				'label' => 'Events',
				'url' => ['/event'],
				'visible' => true,
				'active' => 'event',
				'icon' => 'event'
	        ],
	        [ 
				'label' => 'Filters', 
				'url' => ['/filter'],
				'visible' => true, 
				'active' => 'filter',
				'icon' => 'find_in_page'
	        ],
	        [
				'label' => 'Administration',
				'url' => [],
				'visible' => Yii::$app->user->identity->can('create_users'),
				'active' => 'divider',
				'icon' => ''
	        ],
	        [
				'label' => 'Event types',
				'url' => ['/event-type'],
				'visible' => Yii::$app->user->identity->can('create_users'),
				'active' => 'event-type',
				'icon' => 'event'
	        ],
	        [
				'label' => 'Sec Rules', 
				'url' => ['/sec-rule'],
				'visible' => Yii::$app->user->identity->can('create_users'),
				'active' => 'sec-rule',
				'icon' => 'receipt'
	        ],
	        [
				'label' => 'Users',
				'url' => ['/user'], 
				'visible' => Yii::$app->user->identity->can('create_users'), 
				'active' => 'user',
				'icon' => 'accessibility'
	        ],
	        [
				'label' => 'Roles', 
				'url' => ['/role'], 
				'visible' => Yii::$app->user->identity->can('create_users'), 
				'active' => 'role',
				'icon' => 'perm_identity'
	        ],
>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159
	    ];
	}

	public function getItems()
	{
		return $this->_items;
	}
}