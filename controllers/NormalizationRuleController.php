<?php

namespace app\controllers;


use app\models\Rules\NormalizationParams;
use app\services\RulesService;

/**
 * NormalizationRuleController extends RuleController model.
 */

class NormalizationRuleController extends RuleController
{
    public $rulesService;

    public function __construct($id, $module, $config = [])
    {
        $normalizationParams = new NormalizationParams();
        $this->rulesService = new RulesService($normalizationParams);
        parent::__construct($id, $module, $config);
    }

}