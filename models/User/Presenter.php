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
		//for future use, when there will be form for editing permissions on role
		/*$roles = array_map(function($value)
		{
			return Html::a($value, $value->url);
		}, $this->entity->roles);*/

		$roles = $this->entity->roles;

		return join('<br>', $roles);
	}
}