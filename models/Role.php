<?php

namespace app\models;

use Yii;
use app\models\Role\RolePermission;

/**
 * This is the model class for table "roles".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 *
 * @property Permission[] $permissions
 */
class Role extends \yii\db\ActiveRecord
{
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
		return array_key_exists($permission, $this->permissions);
	}
}
