<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Login form
 */
class LoginForm extends Model
{
	public $username;
	public $password;
	public $rememberMe = true;

	private $_user;


	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['username', 'password'], 'required'],
			['rememberMe', 'boolean'],
			['password', 'validatePassword'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'username' => Yii::t('app', 'Username'),
			'password' => Yii::t('app', 'Password'),
		];
	}

	/**
	 * Validates the password.
	 * This method serves as the inline validation for password.
	 *
	 * @param string $attribute the attribute currently being validated
	 */
	public function validatePassword($attribute)
	{
		if(!$this->hasErrors())
		{
			$user = $this->getUser();

			if(!$user || !$user->validatePassword($this->password))
			{
				$this->addError($attribute, Yii::t('app', 'Incorrect username or password.'));
			}
		}
	}

	/**
	 * Logs in a user using the provided username and password.
	 *
	 * @return boolean whether the user is logged in successfully
	 */
	public function login()
	{
		if($this->validate())
		{
			return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
		}

		return false;
	}

	/**
	 * Finds user by [[username]]
	 *
	 * @return User|null
	 */
	protected function getUser()
	{
		if($this->_user === null)
		{
			$this->_user = User::findByUsername($this->username);
		}

		return $this->_user;
	}
}
