<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "sec_rules".
 *
 * @property integer $id
 * @property string $name
 * @property string $link
 * @property string $state
 */
class SecRule extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $secConfigFile;

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
            [['name', 'link'], 'string', 'max' => 255],
            [['state'], 'boolean'],
            [['secConfigFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'txt']
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
            'link' => 'Link',
            'state' => 'State',
            'secConfigFile' => ''
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->secConfigFile->saveAs('uploads/' . $this->secConfigFile->baseName . '.' . $this->secConfigFile->extension);
            return true;
        } else {
            return false;
        }
    }
}
