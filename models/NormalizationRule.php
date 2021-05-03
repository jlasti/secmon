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
 * @property string $description
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
            [['description'], 'string'],
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
            'description' => 'Description',
            'normalizationRuleFile' => ''
        ];
    }

    public function upload()
    {
        $transaction = Yii::$app->db->beginTransaction();

        if($this->save())
        {
            if($this->state)
//                $files = scandir("/var/www/html/secmon/rules/active/normalization");
                $files = scandir(Yii::getAlias('@app/rules/active/normalization'));
            else
//                $files = scandir("/var/www/html/secmon/rules/available/normalization");
                $files = scandir(Yii::getAlias('@app/rules/available/normalization'));

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
                exec("sudo systemctl restart secmon-normalizer.service");
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
        $fileName = end($linkParts);
        $fileExists = 0;
        $appPath = Yii::getAlias('@app');
        $ruleState = (NormalizationRule::findOne($this->id))->state;         // used to compare with new state, to determine whether to restart normalizer or not


        if($this->state) {      // move rule from available to active
            $this->link = $appPath . '/rules/active/normalization' . '/' . $fileName;
            $files = scandir($appPath . '/rules/active/normalization');
            foreach ($files as $file){
                if($file == $fileName)
                    $fileExists = 1;
            }
            if(!$fileExists)
                exec("mv $appPath/rules/available/normalization/'$fileName' $appPath/rules/active/normalization/'$fileName' >> $appPath/error.log 2>&1");
                if($this->state != $ruleState)
                    exec("sudo systemctl restart secmon-normalizer.service");
        }
        else {              // move rule from active to available
            $this->link = $appPath . '/rules/available/normalization' . '/' . $fileName;
            $files = scandir($appPath . '/rules/available/normalization');
            foreach ($files as $file){
                if($file == $fileName)
                    $fileExists = 1;
            }
            if(!$fileExists)
                exec("mv $appPath/rules/active/normalization/'$fileName' $appPath/rules/available/normalization/'$fileName'  >> $appPath/error.log 2>&1");
                if($this->state != $ruleState)
                    exec("sudo systemctl restart secmon-normalizer.service");
        }
    }
}
