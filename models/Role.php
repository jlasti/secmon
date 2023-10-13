<?php

namespace app\models;

use frostealth\yii2\presenter\traits\PresentableTrait;
use Yii;
use app\models\Role\RolePermission;
use yii\web\Linkable;

/**
 * This is the model class for table "roles".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 *
 * @property Permission[] $permissions
 */
class Role extends \yii\db\ActiveRecord implements Linkable
{
	use PresentableTrait;

	public $permissionList;

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			[
				'class' => \yii\behaviors\SluggableBehavior::className(),
				'attribute' => 'name',
			],
		];
	}

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'roles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'slug'], 'string', 'max' => 255],
            [['name'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]*$/']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'slug' => Yii::t('app', 'Slug'),
        ];
    }

	/**
	 * Finds permissions for this role indexed by slug
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getPermissions()
	{
		return $this->hasMany(Permission::className(), ['id' => 'permission_id'])
			->viaTable(RolePermission::tableName(), ['role_id' => 'id'])
			->indexBy('slug');
	}

	/**
	 * Checks if specified permission is associated with this role
	 *
	 * @param string $permission Permission slug to check
	 * @return boolean
	 */
    public function hasPermission($permission)
	{
		return $this->slug == $permission || array_key_exists($permission, $this->permissions);
	}

	/**
	 * @inheritdoc
	 */
	public function __toString()
	{
		return $this->name;
	}

	/**
	 * @inheritdoc
	 */
	public function getLinks()
	{
		return [
			'self' => ['/role/view', 'id' => $this->id],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function load($data, $formName = null)
	{
		$result = parent::load($data, $formName);

		$this->permissionList = $data['Role']['permissionList'] ?? $this->permissions;

		return $result;
	}

	/**
	 * @inheritdoc
	 */
	public function save($runValidation = true, $attributeNames = null)
	{
		$transaction = Yii::$app->db->beginTransaction();

		if(parent::save($runValidation, $attributeNames))
		{
			RolePermission::deleteAll(['role_id' => $this->id]);

			if(is_array($this->permissionList) && !empty($this->permissionList))
			{
				foreach($this->permissionList as $permission)
				{
					$rolePermission = new RolePermission();
					$rolePermission->permission_id = $permission->id ?? $permission;
					$rolePermission->role_id = $this->id;

					if(!$rolePermission->save())
					{
						$transaction->rollBack();

						return false;
					}
				}
			}

			$transaction->commit();
			return true;
		}

		return false;
	}

	/**
	 * @inheritdoc
	 */
	protected function getPresenterClass()
	{
		return 'app\models\Role\Presenter';
	}
}
