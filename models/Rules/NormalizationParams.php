<?php

namespace app\models\Rules;


/**
 * 
 * Class storing constants for RulesService.
 *
 * @param $AVAILABLE_RULES_PATH  Path to available folder
 * @param $ACTIVE_RULES_PATH  Path to active rules folder
 * @param $RULE_METADATA_PATH  Path to metadata folder
 * @param $BIN_PATH  Path to .bin folder
 */
class NormalizationParams
{
    const AVAILABLE_RULES_PATH = '@app/rules/normalization/available';
    const ACTIVE_RULES_PATH = '@app/rules/normalization/active';
    const RULE_METADATA_PATH = '@app/rules/normalization/ui';
    const BIN_PATH = '@app/rules/normalization/.bin';
}