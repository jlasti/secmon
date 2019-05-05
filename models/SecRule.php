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
 * @property string $type
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
            [['name', 'link', 'type'], 'string', 'max' => 255],
            [['state'], 'boolean'],
            [['secConfigFile'], 'file', 'skipOnEmpty' => !$this->isNewRecord, 'extensions' => 'rule', 'checkExtensionByMimeType' => false],
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
            'type' => 'Type',
            'secConfigFile' => ''
        ];
    }

    public function upload()
    {
    	$transaction = Yii::$app->db->beginTransaction();
        if($this->save())
        {
            if($this->state)
                $files = scandir("/var/www/secmon/rules/active/correlation");
            else
                $files = scandir("/var/www/secmon/rules/available/correlation");

            $fileExists = 0;

            foreach($files as $file){
                if($file == $this->secConfigFile->baseName.'.'.$this->secConfigFile->extension)
                    $fileExists = 1;
            }

            if($fileExists){
                if($this->state)
                    $path = sprintf(Yii::getAlias('@app/rules/active/correlation/%s_%s.%s'), $this->secConfigFile->baseName, $this->id, $this->secConfigFile->extension);
                else
                    $path = sprintf(Yii::getAlias('@app/rules/available/correlation/%s_%s.%s'), $this->secConfigFile->baseName, $this->id, $this->secConfigFile->extension);
            } else {
                if($this->state)
                    $path = sprintf(Yii::getAlias('@app/rules/active/correlation/%s.%s'), $this->secConfigFile->baseName, $this->secConfigFile->extension);
                else
                    $path = sprintf(Yii::getAlias('@app/rules/available/correlation/%s.%s'), $this->secConfigFile->baseName, $this->secConfigFile->extension);
            }

            $this->link = $path;

            if($this->secConfigFile->saveAs($path) && $this->save())
			{
				$transaction->commit();

				return true;
			}

			$this->addError('secConfigFile', 'Error while saving file.');
        }

        $transaction->rollBack();

		return false;
    }

    public function changeRepository()
    {

        $linkParts = explode("/", $this->link);
        $fileName = $linkParts[7];
        $fileExists = 0;

        if ($this->state) {
            $this->link = '/var/www/secmon/rules/active/correlation' . '/' . $fileName;
            $files = scandir('/var/www/secmon/rules/active/correlation');
            foreach ($files as $file) {
                if ($file == $fileName)
                    $fileExists = 1;
            }
            if (!$fileExists)
                exec("mv /var/www/secmon/rules/available/correlation/'$fileName' /var/www/secmon/rules/active/correlation/'$fileName'");
        } else {
            $this->link = '/var/www/secmon/rules/available/correlation' . '/' . $fileName;
            $files = scandir('/var/www/secmon/rules/available/correlation');
            foreach ($files as $file) {
                if ($file == $fileName)
                    $fileExists = 1;
            }
            if (!$fileExists)
                exec("mv /var/www/secmon/rules/active/correlation/'$fileName' /var/www/secmon/rules/available/correlation/'$fileName'");
        }
    }
}
