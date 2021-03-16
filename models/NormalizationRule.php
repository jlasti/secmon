<?php


namespace app\models;

use Yii;
use yii\web\UploadedFile;


/**
 * This is the model class for table "normalization_rules".
 *
 * @property integer $id
 * @property string $name
 * @property string $link
 * @property string $state
 * @property string $type
 * @property string $isDefault
 */
class NormalizationRule extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $normalizationRuleFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'normalization_rules';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'link', 'type'], 'string', 'max' => 255],
            [['state'], 'boolean'],
            [['normalizationRuleFile'], 'file', 'skipOnEmpty' => !$this->isNewRecord, 'extensions' => 'rule', 'checkExtensionByMimeType' => false],
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
            'type' => 'Type',
            'state' => 'Active rule',
            'normalizationRuleFile' => ''
        ];
    }

    public function upload()
    {
        $transaction = Yii::$app->db->beginTransaction();

        if($this->save())
        {
            if($this->state)
                $files = scandir("/var/www/html/secmon/rules/active/normalization");
            else
                $files = scandir("/var/www/html/secmon/rules/available/normalization");

            $fileExists = 0;

            foreach($files as $file){
                if($file == $this->normalizationRuleFile->baseName.'.'.$this->normalizationRuleFile->extension)
                    $fileExists = 1;
            }
            if($fileExists){
                if($this->state)
                    $path = sprintf(Yii::getAlias('@app/rules/active/normalization/%s_%s.%s'), $this->normalizationRuleFile->baseName, $this->id, $this->normalizationRuleFile->extension);
                else
                    $path = sprintf(Yii::getAlias('@app/rules/available/normalization/%s_%s.%s'), $this->normalizationRuleFile->baseName, $this->id, $this->normalizationRuleFile->extension);
            } else {
                if($this->state)
                    $path = sprintf(Yii::getAlias('@app/rules/active/normalization/%s.%s'), $this->normalizationRuleFile->baseName, $this->normalizationRuleFile->extension);
                else
                    $path = sprintf(Yii::getAlias('@app/rules/available/normalization/%s.%s'), $this->normalizationRuleFile->baseName, $this->normalizationRuleFile->extension);
            }

            $this->link = $path;

            if($this->normalizationRuleFile->saveAs($path) && $this->save())
            {

                $transaction->commit();
                return true;
            }

            $this->addError('normalizationRuleFile', 'Error while saving file.');
        }

        $transaction->rollBack();

        return false;
    }

    public function changeRepository(){

        $linkParts = explode("/", $this->link);
        $fileName = $linkParts[7];
        $fileExists = 0;

        if($this->state) {
            $this->link = '/var/www/html/secmon/rules/active/normalization' . '/' . $fileName;
            $files = scandir('/var/www/html/secmon/rules/active/normalization');
            foreach ($files as $file){
                if($file == $fileName)
                    $fileExists = 1;
            }
            if(!$fileExists)
                exec("mv /var/www/html/secmon/rules/default/normalization/'$fileName' /var/www/html/secmon/rules/active/normalization/'$fileName'");
        }
        else {
            $this->link = '/var/www/html/secmon/rules/default/normalization' . '/' . $fileName;
            $files = scandir('/var/www/html/secmon/rules/default/normalization');
            foreach ($files as $file){
                if($file == $fileName)
                    $fileExists = 1;
            }
            if(!$fileExists)
                exec("mv /var/www/html/secmon/rules/active/normalization/'$fileName' /var/www/html/secmon/rules/default/normalization/'$fileName'");
        }
    }
}
