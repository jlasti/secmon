<?php

namespace app\models\NormalizationRule;

use app\models\NormalizationRule;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\FileHelper;
use DateTime;
use DateTimeZone;

/**
 * NormalizationRuleSearch represents the model behind the search form about `app\models\NormalizationRule`.
 */
class NormalizationRuleSearch
{

    public static function getRulesA()
    {
        // Get the list of rule models files in the specified directory
        $ruleFiles = FileHelper::findFiles(Yii::getAlias('@app/rules/normalization/available'), [
            'only' => ['*.rule'],
        ]);

        $ruleModels = [];
        foreach ($ruleFiles as $ruleFile) {
            // Get file creation and modification timestamps
            $statInfo = stat($ruleFile);
            $creationTime = $statInfo['ctime'];
            $lastModifiedTime = $statInfo['mtime'];

            // Create NormalizationRule model and set properties
            $normalizationRule = new NormalizationRule();
            $normalizationRule->filename = basename($ruleFile);
            $normalizationRule->created_at = $creationTime;
            $normalizationRule->modified_at = $lastModifiedTime;
            $normalizationRule->active = NormalizationRuleSearch::checkActive(basename($ruleFile));

            // Add normalizationRule to the collection
            $ruleModels[] = $normalizationRule;
        }

        // Create an array data provider with the loaded data
        $dataProvider = new ArrayDataProvider([
            'allModels' => $ruleModels,
            'pagination' => [
                'pageSize' => 10, // Adjust page size as needed
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Creates data provider filled with UI models for all normalization rules.
     *
     * @param array $params
     * @return ArrayDataProvider
     */
    public static function getRules()
    {
        // Get the list of UI rule models files in the specified directory
        $ruleFiles = FileHelper::findFiles(Yii::getAlias('@app/rules/ui'), [
            'only' => ['*.ui.json'],
        ]);

        $ruleModels = [];
        foreach ($ruleFiles as $ruleFile) {
            $jsonData = json_decode(file_get_contents($ruleFile), true);

            $normalizationRule = new NormalizationRule();
            $normalizationRule->uiFileName = basename($ruleFile);
            $normalizationRule->name = $jsonData['name'];
            $normalizationRule->normalizationRuleFile = $jsonData['file'];
            $normalizationRule->id = $jsonData['id'];
            $normalizationRule->active = (boolean) $jsonData['active'];
            $normalizationRule->created_at = $jsonData['created_at'];
            $normalizationRule->modified_at = $jsonData['modified_at'];

            $ruleModels[] = $normalizationRule;
        }

        // Create an array data provider with the loaded data
        $dataProvider = new ArrayDataProvider([
            'allModels' => $ruleModels,
            'pagination' => [
                'pageSize' => 10, // Adjust page size as needed
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Return NormalizationRule model by name.
     *
     * @param string $name
     * @return mixed
     */
    public static function getRuleByName($name)
    {
        // Get the list of UI rule files in the specified directory
        $ruleFiles = FileHelper::findFiles(Yii::getAlias('@app/rules/ui'), [
            'only' => ['*.ui.json'],
        ]);

        foreach ($ruleFiles as $ruleFile) {
            $jsonData = json_decode(file_get_contents($ruleFile), true);

            if ($jsonData['name'] === $name) {
                $normalizationRule = new NormalizationRule();
                $normalizationRule->uiFileName = $ruleFile;
                $normalizationRule->name = $jsonData['name'];
                $normalizationRule->normalizationRuleFile = $jsonData['file'];
                $normalizationRule->id = $jsonData['id'];
                $normalizationRule->active = (boolean) $jsonData['active'];
                $normalizationRule->created_at = $jsonData['created_at'];
                $normalizationRule->modified_at = $jsonData['modified_at'];

                return $normalizationRule;
            }
        }

        return null;
    }

    /**
     * Updates NormalizationRule model by name.
     *
     * @param NormalizationRule $model
     * @return boolean
     */
    public static function updateRule($model)
    {
        $jsonData = json_decode(file_get_contents($model->uiFileName), true);
        $jsonData['name'] = $model->name;
        $jsonData['normalizationRuleFile'] = $model->normalizationRuleFile;
        $currentDatetime = new DateTime('now', new DateTimeZone('UTC'));
        $jsonData['modified_at'] = $currentDatetime->format('Y-m-d\TH:i:s.u\Z');

        file_put_contents($model->uiFileName, json_encode($jsonData, JSON_PRETTY_PRINT));

        return true;
    }

    private static function checkActive($filename)
    {
        $activeFile = Yii::getAlias('@app/rules/normalization/active') . '/' . $filename;

        return file_exists($activeFile);
    }

}