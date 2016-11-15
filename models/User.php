<?php

namespace app\models;

use frostealth\yii2\presenter\traits\PresentableTrait;
use Yii;
use yii\base\NotSupportedException;
use app\models\User\UserRole;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $auth_key
 *
 * @property Role[] roles
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
	use PresentableTrait;

	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';

	public $passwordText;
	public $rolesList;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

	/**
	 * @inheritdoc
	 */
    public function scenarios()
	{
		return array_merge(parent::scenarios(), [
			static::SCENARIO_CREATE => ['first_name', 'last_name', 'username', 'email', 'passwordText'],
			static::SCENARIO_UPDATE => ['first_name', 'last_name', 'username', 'email', 'passwordText'],
		]);
	}

	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['first_name', 'last_name', 'username', 'email'], 'required', 'on' => [static::SCENARIO_CREATE, static::SCENARIO_UPDATE]],
			['email', 'email'],
			['passwordText', 'required', 'on' => static::SCENARIO_CREATE],
			['passwordText', 'string', 'min' => 6],
            [['first_name', 'last_name', 'username', 'password', 'email', 'auth_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
			'passwordText' => Yii::t('app', 'Password'),
            'email' => Yii::t('app', 'Email'),
            'auth_key' => Yii::t('app', 'Auth Key'),
        ];
    }

    /**
	 * Finds roles for user indexed by slug
	 *
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::className(), ['id' => 'role_id'])
			->viaTable(UserRole::tableName(), ['user_id' => 'id'])
			->indexBy('slug');
    }

	/**
	 * Checks if user has specified role
	 *
	 * @param string $role Role slug to check
	 * @return boolean
	 */
    public function hasRole($role)
	{
		return array_key_exists($role, $this->roles);
	}

	/**
	 * Check if user has specified permission
	 *
	 * @param string $permission Permission slug to check
	 * @return boolean
	 */
    public function can($permission)
	{
		foreach($this->roles as $role)
		{
			if($role->hasPermission($permission))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentity($id)
	{
		return static::findOne($id);
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken($token, $type = null)
	{
		throw new NotSupportedException('User::findIdentityByAccessToken is currently not supported.');
	}

	/**
	 * Finds user by username
	 *
	 * @param string $username
	 * @return static|null
	 */
	public static function findByUsername($username)
	{
		return static::findOne(['username' => $username]);
	}

	/**
	 * @inheritdoc
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @inheritdoc
	 */
	public function getAuthKey()
	{
		return $this->auth_key;
	}

	/**
	 * @inheritdoc
	 */
	public function validateAuthKey($authKey)
	{
		return $this->auth_key === $authKey;
	}

	/**
	 * Validates password
	 *
	 * @param string $password password to validate
	 * @return boolean if password provided is valid for current user
	 */
	public function validatePassword($password)
	{
		return Yii::$app->security->validatePassword($password, $this->password);
	}

	/**
	 * Hashes the password and sets it
	 *
	 * @param string $password Password to set
	 */
	public function setPassword($password)
	{
		$this->password = Yii::$app->security->generatePasswordHash($password);
	}

	/**
	 * Generates random auth key for user
	 */
	public function generateAuthKey()
	{
		$this->auth_key = Yii::$app->security->generateRandomString();
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert)
	{
		//could've done it with behavior but why create behavior for one thing
		if(parent::beforeSave($insert))
		{
			switch($this->scenario)
			{
				case static::SCENARIO_CREATE:
					$this->generateAuthKey();
					break;
			}
		}

		if(strlen($this->passwordText) != 0)
		{
			$this->setPassword($this->passwordText);
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function load($data, $formName = null)
	{
		$result = parent::load($data, $formName);

		$this->rolesList = $data['User']['rolesList'] ?? $this->roles;

		if(!is_array($this->rolesList) || empty($this->rolesList))
		{
			//TODO: Global settings for default role....
			$this->rolesList = [1];
		}

		return $result;
	}

	/**
	 * @inheritdoc
	 */
	public function save($runValidation = true, $attributeNames = null)
	{
		//TODO: Refactor (3:00, really do not have energy to do it nicely), also in Role::save()
		$transaction = Yii::$app->db->beginTransaction();

		if(parent::save($runValidation, $attributeNames))
		{
			UserRole::deleteAll(['user_id' => $this->id]);

			foreach($this->rolesList as $role)
			{
				$userRole = new UserRole();
				$userRole->role_id = $role->id ?? $role;
				$userRole->user_id = $this->id;

				if(!$userRole->save())
				{
					$transaction->rollBack();

					return false;
				}
			}

			$transaction->commit();
			return true;
		}

		return false;
	}

	/**
	 * @return string|array
	 */
	protected function getPresenterClass()
	{
		return 'app\models\User\Presenter';
	}
}
