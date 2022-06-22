<?php
namespace app\components;

class AccessRule extends \yii\filters\AccessRule
{
	/**
	 * @inheritdoc
	 */
	protected function matchRole($user)
	{
		if(empty($this->roles))
		{
			return true;
		}

		foreach($this->roles as $role)
		{
			if(($role === '?' && $user->isGuest)
				|| ($role === '@' && !$user->isGuest)
				|| ($user->identity != null && $user->identity->can($role)))
			{
				return true;
			}
		}

		return false;
	}
}