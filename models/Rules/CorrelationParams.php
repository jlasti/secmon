<?php

namespace app\models\Rules;


/**
 * Class storing constants for working with Correlation rules.
 *
 * @param $AVAILABLE_RULES_PATH  Path to available folder
 * @param $ACTIVE_RULES_PATH  Path to active rules folder
 * @param $RULE_METADATA_PATH  Path to metadata folder
 * @param $BIN_PATH  Path to .bin folder
 */
class CorrelationParams
{
    const RULES_TYPE = "correlation";
    const AVAILABLE_RULES_PATH = '@app/rules/correlation/available';
    const ACTIVE_RULES_PATH = '@app/rules/correlation/active';
    const RULE_METADATA_PATH = '@app/rules/correlation/ui';
    const BIN_PATH = '@app/rules/correlation/.bin';
    const RESTART_FILE = '@app/rules/corr_restart.req';
}