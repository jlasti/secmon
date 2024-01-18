<?php

namespace app\models\NormalizationRule;

use Yii;
use app\models\NormalizationRule;
use yii\data\ArrayDataProvider;
use yii\helpers\FileHelper;

/**
 * NormalizationRuleSearch represents the model behind the search form about `app\models\NormalizationRule`.
 */
class NormalizationRuleSearch
{
    const PAGE_SIZE = 10;
    const AVAILABLE_RULES_PATH = '@app/rules/normalization/available';
    const ACTIVE_RULES_PATH = '@app/rules/normalization/active';
    const RULE_METADATA_PATH = '@app/rules/normalization/ui';

    /**
     * Returns data provider filled with all available NormalizationRule models.
     *
     * @param array $params
     * @return ArrayDataProvider
     */
    public static function getAllRules()
    {
        // Get the list of all available normalization rule files.
        $ruleFiles = FileHelper::findFiles(Yii::getAlias(NormalizationRuleSearch::AVAILABLE_RULES_PATH), [
            'only' => ['*.rule'],
        ]);

        $ruleModels = [];
        foreach ($ruleFiles as $ruleFile) {
            $ruleModels[] = NormalizationRuleSearch::createRuleModel($ruleFile);
        }

        // Create and return array data provider with the loaded data. 
        $dataProvider = new ArrayDataProvider([
            'allModels' => $ruleModels,
            'pagination' => [
                'pageSize' => NormalizationRuleSearch::PAGE_SIZE,
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
                return NormalizationRuleSearch::createRuleModel($ruleFile);
            }
        }
        return null;
    }

    /**
     * Updates NormalizationRule and metadata file.
     *
     * @param NormalizationRule $model
     * @return boolean
     */
    public static function updateRule($model)
    {
        $ruleFileName = $model->ruleFileName;
        $metaFileName = pathinfo($ruleFileName, PATHINFO_FILENAME) . ".ui.json"; // Metadata file name (ex. apache.ui.json)
        $ruleMetaFile = NormalizationRuleSearch::findMetaFileByRuleName($ruleFileName); // Return json data from metadata file
        $metaFilePath = Yii::getAlias(NormalizationRuleSearch::RULE_METADATA_PATH) . '/' . $metaFileName; // Full path to metadata file

        // file_put_contents('/var/www/html/secmon/error.log', $model->toArray());

        if (is_null($ruleMetaFile)) {
            // Metadata files does not exists.
            file_put_contents($metaFilePath, json_encode(['name' => $model->name])); // Add 'name' key with value set from WebUI.

            if ($model->active == 1 && NormalizationRuleSearch::isRuleActive($ruleFileName) == 0) {
                // Rule status change to 'active'.
                NormalizationRuleSearch::activateRule($ruleFileName);
            } else if ($model->active == 0 && NormalizationRuleSearch::isRuleActive($ruleFileName) == 1) {
                // Rule status change to 'available'.
                NormalizationRuleSearch::deactivateRule($ruleFileName);
            }
        } else {
            // Metadata file exists, update key 'name'.
            $ruleMetaFile['name'] = $model->name;
            file_put_contents($metaFilePath, json_encode($ruleMetaFile));
            if ($model->active == 1 && NormalizationRuleSearch::isRuleActive($ruleFileName) == 0) {
                // Rule status change to 'active'.
                NormalizationRuleSearch::activateRule($ruleFileName);
            } else if ($model->active == 0 && NormalizationRuleSearch::isRuleActive($ruleFileName) == 1) {
                // Rule status change to 'available'.
                NormalizationRuleSearch::deactivateRule($ruleFileName);
            }
        }
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
        $ruleMetaFile = NormalizationRuleSearch::findMetaFileByRuleName(basename($ruleFile));
        if (!is_null($ruleMetaFile)) {
            $normalizationRule->name = $ruleMetaFile['name'];
        }

        // Set properties of NormalizationRule model.
        $normalizationRule->ruleFileName = basename($ruleFile);
        $normalizationRule->size = $fileSize;
        $normalizationRule->modified_at = date('d.m.Y H:i:s', $lastModifiedTime);
        $normalizationRule->active = NormalizationRuleSearch::isRuleActive(basename($ruleFile));

        return $normalizationRule;
    }

    // Created hardlink from available .rule directory to active rules directory.
    private static function activateRule($ruleFileName)
    {
        $targetPath = Yii::getAlias(NormalizationRuleSearch::ACTIVE_RULES_PATH) . '/' . $ruleFileName;
        $fromPath = Yii::getAlias(NormalizationRuleSearch::AVAILABLE_RULES_PATH) . '/' . $ruleFileName;
        return link($fromPath, $targetPath);
    }

    // Removes file(hardlink) from directory 'active'.
    private static function deactivateRule($ruleFileName)
    {
        $targetPath = Yii::getAlias(NormalizationRuleSearch::ACTIVE_RULES_PATH) . '/' . $ruleFileName;
        return unlink($targetPath);
    }

    // Return metadata json data, if found by metadata rule file name. Null if not found.
    private static function findMetaFileByRuleName($ruleFileName)
    {
        $metaFiles = FileHelper::findFiles(Yii::getAlias(NormalizationRuleSearch::RULE_METADATA_PATH), [
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
        $activeFile = Yii::getAlias(NormalizationRuleSearch::ACTIVE_RULES_PATH) . '/' . $ruleFileName;
        return file_exists($activeFile);
    }

}