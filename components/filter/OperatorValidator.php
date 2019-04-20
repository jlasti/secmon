<?php
namespace app\components\filter;

use yii\base\InvalidConfigException;
use yii\validators\Validator;

/**
 * @property $class BaseFilterRule
 */

class OperatorValidator extends Validator
{
	public $ruleClass;

	/**
	 * @inheritdoc
	 *
	 * @throws InvalidConfigException
	 */
	public function validateValue($value)
	{
		if($this->ruleClass == null)
		{
			throw new InvalidConfigException('OperatorValidator::ruleClass must be set.');
		}

		$operators = call_user_func($this->ruleClass . '::operators');

		if(!in_array($value, $operators))
		{
			$error = sprintf('Operator %s not allowed in class %s', $value, $this->ruleClass);

			return [$error, []];
		}

		return null;
	}
}