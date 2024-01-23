<?php

namespace app\controllers;


use app\models\Rules\CorrelationParams;
use app\services\RulesService;

/**
 * CorrelationRuleController extends RuleController model.
 */

class CorrelationRuleController extends RuleController
{
    public $rulesService;
    
    public function __construct($id, $module, $config = [])
    {
        $normalizationParams = new CorrelationParams();
        $this->rulesService = new RulesService($normalizationParams);
        parent::__construct($id, $module, $config);
    }
    
}