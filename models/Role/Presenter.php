<?php
namespace app\models\Role;

use Yii;
use app\models\BasePresenter;
use yii\helpers\Html;

class Presenter extends BasePresenter
{
	public function getPermissions()
	{
		$permissions = $this->entity->permissions;

		return sprintf('<li>%s</li>', join('</li><li>', $permissions));
	}
}