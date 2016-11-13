<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sec_rules".
 *
 * @property integer $id
 * @property string $name
 * @property string $content
 */
class SecRules extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sec_rules';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'content' => 'Content',
        ];
    }
}
