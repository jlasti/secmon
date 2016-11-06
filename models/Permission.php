<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "permissions".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 *
 * @property RelRolePermission[] $relRolePermissions
 */
class Permission extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'permissions';
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
     * @return \yii\db\ActiveQuery
     */
    public function getRelRolePermissions()
    {
        return $this->hasMany(RelRolePermission::className(), ['permission_id' => 'id']);
    }
}
