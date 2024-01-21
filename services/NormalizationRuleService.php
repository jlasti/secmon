<?php

namespace app\services;

use Yii;
use app\models\NormalizationRule;
use yii\data\ArrayDataProvider;
use yii\helpers\FileHelper;

/**
 * NormalizationRuleService is responsible for managing normalization rules.
 */
class NormalizationRuleService
{
    const PAGE_SIZE = 10;
    const AVAILABLE_RULES_PATH = '@app/rules/normalization/available';
    const ACTIVE_RULES_PATH = '@app/rules/normalization/active';
    const RULE_METADATA_PATH = '@app/rules/normalization/ui';
    const BIN_PATH = '@app/rules/normalization/.bin';

    /**
     * Safely imports new rule using cURL.
     *
     * @param array $params
     * @return mixed false if import was not successful. $newRuleFileName if successful
     */
    public static function importNewRule($model)
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
        if (!NormalizationRuleService::isValidFileName($newRuleFileName))
            return false;

        $newRuleFilePath = Yii::getAlias(NormalizationRuleService::AVAILABLE_RULES_PATH) . '/' . $newRuleFileName;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($curl);
        curl_close($curl);

        if (!$response)
            return false;

        file_put_contents($newRuleFilePath, $response);
        if ($active == 1)
            NormalizationRuleService::activateRule($newRuleFileName);

        return $newRuleFileName;
    }

    /**
     * Returns data provider filled with all available NormalizationRule models.
     *
     * @param array $params
     * @return ArrayDataProvider
     */
    public static function getAllRules()
    {
        // Get the list of all available normalization rule files.
        $ruleFiles = FileHelper::findFiles(Yii::getAlias(NormalizationRuleService::AVAILABLE_RULES_PATH), [
            'only' => ['*.rule'],
        ]);

        $ruleModels = [];
        foreach ($ruleFiles as $ruleFile) {
            $ruleModels[] = NormalizationRuleService::createRuleModel($ruleFile);
        }

        // Create and return array data provider with the loaded data. 
        $dataProvider = new ArrayDataProvider([
            'allModels' => $ruleModels,
            'pagination' => [
                    'pageSize' => NormalizationRuleService::PAGE_SIZE,
                ],
        ]);
        return $dataProvider;
    }

    /**
     * Return one NormalizationRule model by rule file name.
     *
     * @param string $rulefileName
     * @return mixed
     */
    public static function getRuleByFileName($rulefileName)
    {
        // Get the list of all available normalization rule files.
        $ruleFiles = FileHelper::findFiles(Yii::getAlias('@app/rules/normalization/available'), [
            'only' => ['*.rule'],
        ]);

        foreach ($ruleFiles as $ruleFile) {
            if (basename($ruleFile) === $rulefileName) {
                // Find one available rule by matching file name.
                return NormalizationRuleService::createRuleModel($ruleFile);
            }
        }
        return null;
    }

    /**
     * Updates NormalizationRule file and metadata file.
     *
     * @param NormalizationRule $model
     * @return boolean
     */
    public static function updateRule($model)
    {
        $ruleFileName = $model->ruleFileName;
        $metaFileName = pathinfo($ruleFileName, PATHINFO_FILENAME) . ".ui.json"; // Metadata file name (ex. apache.ui.json)
        $ruleMetaFile = NormalizationRuleService::findMetaFileByRuleName($ruleFileName); // Return json data from metadata file
        $metaFilePath = Yii::getAlias(NormalizationRuleService::RULE_METADATA_PATH) . '/' . $metaFileName; // Full path to metadata file

        // file_put_contents('/var/www/html/secmon/error.log', $model->toArray());

        if (is_null($ruleMetaFile)) {
            // Metadata files does not exists.
            file_put_contents($metaFilePath, json_encode(['name' => $model->name])); // Add 'name' key with value set from WebUI.
        } else {
            // Metadata file exists, update key 'name'.
            $ruleMetaFile['name'] = $model->name;
            file_put_contents($metaFilePath, json_encode($ruleMetaFile));

        }
        if ($model->active == 1 && NormalizationRuleService::isRuleActive($ruleFileName) == 0) {
            // Rule status change to 'active'.
            NormalizationRuleService::activateRule($ruleFileName);
        } else if ($model->active == 0 && NormalizationRuleService::isRuleActive($ruleFileName) == 1) {
            // Rule status change to 'available'.
            NormalizationRuleService::deactivateRule($ruleFileName);
        }
        file_put_contents(Yii::getAlias(NormalizationRuleService::AVAILABLE_RULES_PATH) . '/' . $ruleFileName, $model->content);
        return true; // true, if update was successful.
    }

    /**
     * Crates NormalizationRule model from ruleFile.
     *
     * @param string $ruleFile
     * @return NormalizationRule
     */
    private static function createRuleModel($ruleFile)
    {
        $normalizationRule = new NormalizationRule();
        // Extract information about file to NormalizationRule model.
        $statInfo = stat($ruleFile);
        // $fileSize = round($statInfo['size'] / 1024, 0); // convertion to KB.
        $fileSize = $statInfo['size'];
        $lastModifiedTime = $statInfo['mtime'];

        // If metadata file exists, read attributes from it to NormalizationRule model.
        $ruleMetaFile = NormalizationRuleService::findMetaFileByRuleName(basename($ruleFile));
        if (!is_null($ruleMetaFile)) {
            $normalizationRule->name = $ruleMetaFile['name'];
        }

        // Set properties of NormalizationRule model.
        $normalizationRule->ruleFileName = basename($ruleFile);
        $normalizationRule->size = $fileSize;
        $normalizationRule->modified_at = date('d.m.Y H:i:s', $lastModifiedTime);
        $normalizationRule->active = NormalizationRuleService::isRuleActive(basename($ruleFile));

        // Load the content of the file.
        $normalizationRule->content = file_get_contents($ruleFile);

        return $normalizationRule;
    }

    /**
     * Deletes NormalizationRule file from active and available folders.
     *
     * @param string $ruleFileName
     * @return boolean
     */
    public static function deleteRule($ruleFileName)
    {
        $fromPath = Yii::getAlias(NormalizationRuleService::AVAILABLE_RULES_PATH) . '/' . $ruleFileName;
        $toPath = Yii::getAlias(NormalizationRuleService::BIN_PATH) . '/' . $ruleFileName;
        $metadataPath = Yii::getAlias(NormalizationRuleService::RULE_METADATA_PATH) . '/' . $ruleFileName;
        if (NormalizationRuleService::isRuleActive($ruleFileName)) {
            NormalizationRuleService::deactivateRule($ruleFileName);
        }
        rename($fromPath, $toPath);
        if (file_exists($metadataPath)) {
            unlink($metadataPath);
        }
        return true;
    }

    /**
     * Deletes all active NormalizationRule files from active folders.
     *
     * @return array Returns array of full path to previously active rules.
     */
    public static function deleteActiveRules()
    {
        $active = FileHelper::findFiles(Yii::getAlias(NormalizationRuleService::ACTIVE_RULES_PATH), [
            'only' => ['*.rule'],
        ]);
        foreach ($active as $file) {
            unlink($file);
        }
        return $active;
    }

    /**
     * Reactivates NormalizationRules from previously active rules provided in @param.
     *
     * @param array $activeRules
     * @return int Returns 1 if reactivation was successful.
     */
    public static function reactiveRules($activeRules)
    {
        $available = FileHelper::findFiles(Yii::getAlias(NormalizationRuleService::AVAILABLE_RULES_PATH), [
            'only' => ['*.rule'],
        ]);
        $availableBNames = array();
        foreach ($available as $FPactive) { // Convert full path to base name
            $availableBNames[] = basename($FPactive);
        }
        foreach ($activeRules as $rule) {
            $ruleBName = basename($rule);
            if (in_array($ruleBName, $availableBNames, true)) { // Ensure previously active rule was not deleted in new update.
                NormalizationRuleService::activateRule($ruleBName);
            }
        }
        return 1;
    }

    // Created hardlink from available .rule directory to active rules directory.
    private static function activateRule($ruleFileName)
    {
        $targetPath = Yii::getAlias(NormalizationRuleService::ACTIVE_RULES_PATH) . '/' . $ruleFileName;
        $fromPath = Yii::getAlias(NormalizationRuleService::AVAILABLE_RULES_PATH) . '/' . $ruleFileName;
        return link($fromPath, $targetPath);
    }

    // Removes file(hardlink) from directory 'active'.
    private static function deactivateRule($ruleFileName)
    {
        $targetPath = Yii::getAlias(NormalizationRuleService::ACTIVE_RULES_PATH) . '/' . $ruleFileName;
        return unlink($targetPath);
    }

    // Return metadata json data, if found by metadata rule file name. Null if not found.
    private static function findMetaFileByRuleName($ruleFileName)
    {
        $metaFiles = FileHelper::findFiles(Yii::getAlias(NormalizationRuleService::RULE_METADATA_PATH), [
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
    private static function isRuleActive($ruleFileName)
    {
        $activeFile = Yii::getAlias(NormalizationRuleService::ACTIVE_RULES_PATH) . '/' . $ruleFileName;
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