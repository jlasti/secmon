<?php

namespace app\services;

use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\FileHelper;
use app\models\Rules\Rule;

/**
 * RulesService is responsible for managing correlation or normalization rules directly in file system.
 * $parameters representes model with constants(parameters). 
 * @see NormalizationParams or CorrelationParams
 */
class RulesService
{
    const PAGE_SIZE = 15; // Size of page on 'index'
    private $parameters;

    function __construct($params)
    {
        $this->parameters = $params;
    }

    /**
     * Safely imports new rule using cURL.
     *
     * @param Rule $model
     * @return mixed false if import was not successful. $newRuleFileName if successful.
     */
    public function importNewRule($model)
    {
        $active = $model->active;
        $url = $model->content;

        # Validate url
        if (!filter_var($url, FILTER_VALIDATE_URL))
            return false;

        # Check if safe file name
        if (is_null($model->name)) {
            $newRuleFileName = basename($url);
        } else {
            $newRuleFileName = $model->name;
        }
        if (!RulesService::isValidFileName($newRuleFileName))
            return false;

        $newRuleFilePath = Yii::getAlias($this->parameters::AVAILABLE_RULES_PATH) . '/' . $newRuleFileName;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($curl);
        curl_close($curl);

        if (!$response)
            return false;

        file_put_contents($newRuleFilePath, $response);
        if ($active == 1)
            RulesService::activateRule($newRuleFileName);

        return $newRuleFileName;
    }

    /**
     * Returns data provider filled with all available Rule models.
     *
     * @return ArrayDataProvider
     */
    public function getAllRules()
    {
        // Get the list of all available rule files.
        $ruleFiles = FileHelper::findFiles(Yii::getAlias($this->parameters::AVAILABLE_RULES_PATH), [
            'only' => ['*.rule'],
        ]);

        $ruleModels = [];
        foreach ($ruleFiles as $ruleFile) {
            $ruleModels[] = RulesService::createRuleModel($ruleFile);
        }

        // Create and return array data provider with the loaded rule models. 
        $dataProvider = new ArrayDataProvider([
            'allModels' => $ruleModels,
            'pagination' => [
                'pageSize' => RulesService::PAGE_SIZE,
            ],
        ]);
        return $dataProvider;
    }

    /**
     * Return one Rule model by rule file name.
     *
     * @param string $rulefileName
     * @return mixed
     */
    public function getRuleByFileName($rulefileName)
    {
        // Get the list of all available rule files.
        $ruleFiles = FileHelper::findFiles(Yii::getAlias($this->parameters::AVAILABLE_RULES_PATH), [
            'only' => ['*.rule'],
        ]);

        foreach ($ruleFiles as $ruleFile) {
            if (basename($ruleFile) === $rulefileName) {
                // Find one available rule by matching file name.
                return RulesService::createRuleModel($ruleFile);
            }
        }
        return null;
    }

    /**
     * Updates Rule file and metadata file. Using updated model from view.
     *
     * @param Rule $model
     * @return boolean
     */
    public function updateRule($model)
    {
        $ruleFileName = $model->ruleFileName;
        $metaFileName = pathinfo($ruleFileName, PATHINFO_FILENAME) . ".ui.json"; // Metadata file name (ex. apache.ui.json)
        $metaFileData = RulesService::findMetaFileByRuleName($ruleFileName); // Return json data from metadata file
        $metaFilePath = Yii::getAlias($this->parameters::RULE_METADATA_PATH) . '/' . $metaFileName; // Full path to metadata file

        // file_put_contents('/var/www/html/secmon/error.log', $model->toArray());

        if (is_null($metaFileData)) {
            // Metadata files does not exists.
            file_put_contents($metaFilePath, json_encode(['name' => $model->name])); // Add 'name' key with value from model.
        } else {
            // Metadata file exists, update key 'name'.
            $metaFileData['name'] = $model->name;
            file_put_contents($metaFilePath, json_encode($metaFileData));
        }
        if ($model->active == 1 && RulesService::isRuleActive($ruleFileName) == 0) {
            // Rule status changed to 'active'.
            RulesService::activateRule($ruleFileName);
        } else if ($model->active == 0 && RulesService::isRuleActive($ruleFileName) == 1) {
            // Rule status changed to 'available'.
            RulesService::deactivateRule($ruleFileName);
        }
        file_put_contents(Yii::getAlias($this->parameters::AVAILABLE_RULES_PATH) . '/' . $ruleFileName, $model->content);
        return true; // true, if update was successful.
    }

    /**
     * Crates Rule model from rule file.
     *
     * @param string $ruleFile
     * @return Rule
     */
    private function createRuleModel($ruleFile)
    {
        $rule = new Rule();

        // Extract information about file to Rule model.
        $statInfo = stat($ruleFile);
        // $fileSize = round($statInfo['size'] / 1024, 0); // convertion to KB.
        $lastModifiedTime = $statInfo['mtime'];
        $lastAccessTime = $statInfo['atime'];
        $rule->size = $statInfo['size'];
        $rule->gid = $statInfo['gid'];
        $rule->uid = $statInfo['uid'];

        // If metadata file exists, read attributes from it to Rule model.
        $ruleMetaFile = RulesService::findMetaFileByRuleName(basename($ruleFile));
        if (!is_null($ruleMetaFile)) {
            $rule->name = $ruleMetaFile['name'];
        }

        // Set properties of Rule model.
        $rule->ruleFileName = basename($ruleFile);
        $rule->modified_at = date('d.m.Y H:i:s', $lastModifiedTime);
        $rule->accessed_at = date('d.m.Y H:i:s', $lastAccessTime);
        $rule->active = RulesService::isRuleActive(basename($ruleFile));

        // Load the content of the file.
        $rule->content = file_get_contents($ruleFile);

        return $rule;
    }

    /**
     * Deletes Rule file from active and available folders.
     *
     * @param string $ruleFileName
     * @return boolean
     */
    public function deleteRule($ruleFileName)
    {
        $fromPath = Yii::getAlias($this->parameters::AVAILABLE_RULES_PATH) . '/' . $ruleFileName;
        $toPath = Yii::getAlias($this->parameters::BIN_PATH) . '/' . $ruleFileName;
        $metadataPath = Yii::getAlias($this->parameters::RULE_METADATA_PATH) . '/' . $ruleFileName;
        if (RulesService::isRuleActive($ruleFileName)) {
            RulesService::deactivateRule($ruleFileName);
        }
        rename($fromPath, $toPath);
        if (file_exists($metadataPath)) {
            unlink($metadataPath);
        }
        return true;
    }

    /**
     * Deletes all active Rule files from active folders.
     *
     * @return array Returns array of full path to previously active rules.
     */
    public function deleteActiveRules()
    {
        $active = FileHelper::findFiles(Yii::getAlias($this->parameters::ACTIVE_RULES_PATH), [
            'only' => ['*.rule'],
        ]);
        foreach ($active as $file) {
            unlink($file);
        }
        return $active;
    }

    /**
     * Reactivates Rules from previously active rules provided in @param.
     *
     * @param array $activeRules
     * @return int Returns 1 if reactivation was successful.
     */
    public function reactiveRules($activeRules)
    {
        $available = FileHelper::findFiles(Yii::getAlias($this->parameters::AVAILABLE_RULES_PATH), [
            'only' => ['*.rule'],
        ]);
        $availableBNames = array();
        foreach ($available as $FPactive) { // Convert full path to base name
            $availableBNames[] = basename($FPactive);
        }
        foreach ($activeRules as $rule) {
            $ruleBName = basename($rule);
            if (in_array($ruleBName, $availableBNames, true)) { // Ensure previously active rule was not deleted in new update.
                RulesService::activateRule($ruleBName);
            }
        }
        return 1;
    }

    // Created hardlink from available .rule directory to active rules directory.
    private function activateRule($ruleFileName)
    {
        $targetPath = Yii::getAlias($this->parameters::ACTIVE_RULES_PATH) . '/' . $ruleFileName;
        $fromPath = Yii::getAlias($this->parameters::AVAILABLE_RULES_PATH) . '/' . $ruleFileName;
        return link($fromPath, $targetPath);
    }

    // Removes file(hardlink) from directory 'active'.
    private function deactivateRule($ruleFileName)
    {
        $targetPath = Yii::getAlias($this->parameters::ACTIVE_RULES_PATH) . '/' . $ruleFileName;
        return unlink($targetPath);
    }

    // Return metadata .json data, if found by metadata rule file name. Null if not found.
    private function findMetaFileByRuleName($ruleFileName)
    {
        $metaFiles = FileHelper::findFiles(Yii::getAlias($this->parameters::RULE_METADATA_PATH), [
            'only' => ['*ui.json'],
        ]);

        foreach ($metaFiles as $metaFile) {
            if (basename($metaFile, ".ui.json") === basename($ruleFileName, ".rule")) {
                $jsonData = json_decode(file_get_contents($metaFile), true);
                return $jsonData;
            }
        }
        return null;
    }

    // Return true, if .rule file is present in 'active' directory (checks by rule file name)
    private function isRuleActive($ruleFileName)
    {
        $activeFile = Yii::getAlias($this->parameters::ACTIVE_RULES_PATH) . '/' . $ruleFileName;
        return file_exists($activeFile);
    }

    // Check for safe file name using regex
    private static function isValidFileName($input)
    {
        $pattern = '/^[a-zA-Z0-9.\-_]+$/';

        if (preg_match($pattern, $input)) {
            return true;
        }
        return false;
    }

}