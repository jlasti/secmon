<?php

namespace app\components;

use Yii;

class Navigation extends \yii\base\Component
{
	private $_items;

	public function init()
	{
		$this->_items = [
	        ['label' => 'Events',
	         'url' => ['/event'],
	         'visible' => true,
	         'active' => 'event',
	         'icon' => 'event'
	        ],
	        ['label' => 'Filters', 
	         'url' => ['/filter'],
	         'visible' => true, 
	         'active' => 'filter',
	         'icon' => 'find_in_page'
	        ],
	        ['label' => 'Administration',
	         'url' => [],
	         'visible' => Yii::$app->user->identity->can('create_users'),
	         'active' => 'divider',
	         'icon' => ''
	        ],
	        ['label' => 'Event types',
	         'url' => ['/event-type'],
	         'visible' => Yii::$app->user->identity->can('create_users'),
	         'active' => 'event-type',
	         'icon' => 'event'
	        ],
	        ['label' => 'Sec Rules', 
	         'url' => ['/sec-rule'],
	         'visible' => Yii::$app->user->identity->can('create_users'),
	         'active' => 'sec-rule',
	         'icon' => 'receipt'
	        ],
	        ['label' => 'Users',
	         'url' => ['/user'], 
	         'visible' => Yii::$app->user->identity->can('create_users'), 
	         'active' => 'user',
	         'icon' => 'accessibility'
	        ],
	        ['label' => 'Roles', 
	         'url' => ['/role'], 
	         'visible' => Yii::$app->user->identity->can('create_users'), 
	         'active' => 'role',
	         'icon' => 'perm_identity'
	        ],
	    ];
	}

	public function getItems()
	{
		return $this->_items;
	}
}