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
            [['secConfigFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'txt', 'checkExtensionByMimeType' => false],
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
            'state' => 'Active rule',
            'secConfigFile' => ''
        ];
    }

    public function upload()
    {
    	$transaction = Yii::$app->db->beginTransaction();

        if($this->save())
        {
        	$path = sprintf(Yii::getAlias('@app/uploads/%s.%s'), $this->secConfigFile->baseName, $this->secConfigFile->extension);

            if($this->secConfigFile->saveAs($path))
			{
				$transaction->commit();

				return true;
			}

			$this->addError('secConfigFile', 'Error while saving file.');
        }

        $transaction->rollBack();

		return false;
    }
}
