<?php
namespace app\models\User;

use Yii;
use app\models\BasePresenter;
use yii\helpers\Html;

class Presenter extends BasePresenter
{
	public function attributeLabels()
	{
		return [
			'roles' => 'Roles assigned',
		];
	}

	public function getRoles()
	{
		$roles = array_map(function($value)
		{
			return Html::a($value, $value->links['self']);
		}, $this->entity->roles);

		return join('<br>', $roles);
	}

	#warning - TODO
	public function getMainRole()
	{
		return 'rola'; 
	}
}