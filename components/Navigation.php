<?php

namespace app\components;

use Yii;

class Navigation extends \yii\base\Component
{
	private $_items;

	public function init()
	{
		$this->_items =
			[
				[
					'label' => 'Security Events',
					'url' => ['/security-events'],
					'visible' => true,
					'active' => 'security-events',
					'icon' => 'lock'
				],
				[
					'label' => 'Clustered Events',
					'url' => ['/events-clustered-runs'],
					'visible' => true,
					'active' => 'events-clustered-runs',
					'icon' => 'timeline'
				],
				[
					'label' => 'Dashboard',
					'url' => ['/view'],
					'visible' => true,
					'active' => 'view',
					'icon' => 'dashboard'
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
					'label' => 'Correlation Rules',
					'url' => ['/sec-rule'],
					'visible' => Yii::$app->user->identity->can('create_users'),
					'active' => 'sec-rule',
					'icon' => 'receipt'
				],
				[
					'label' => 'Normalization Rules',
					'url' => ['/normalization-rule'],
					'visible' => Yii::$app->user->identity->can('create_users'),
					'active' => 'normalization-rule',
					'icon' => 'receipt'

				],
				[
					'label' => 'Network model',
					'url' => ['/network-model'],
					'visible' => Yii::$app->user->identity->can('create_users'),
					'active' => 'network-model',
					'icon' => 'laptop'
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
			];
	}

	public function getItems()
	{
		return $this->_items;
	}
}
