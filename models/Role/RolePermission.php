<?php

namespace app\models\Role;

use Yii;

/**
 * This is the model class for table "rel_role_permission".
 *
 * @property integer $role_id
 * @property integer $permission_id
 *
 * @property Permissions $permission
 * @property Roles $role
 */
class RolePermission extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rel_role_permission';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'permission_id'], 'integer'],
            [['permission_id'], 'exist', 'skipOnError' => true, 'targetClass' => Permissions::className(), 'targetAttribute' => ['permission_id' => 'id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::className(), 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id' => Yii::t('app', 'Role ID'),
            'permission_id' => Yii::t('app', 'Permission ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermission()
    {
        return $this->hasOne(Permissions::className(), ['id' => 'permission_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Roles::className(), ['id' => 'role_id']);
    }
}
