<?php
namespace app\components\filter;

use app\components\filter\BaseFilterRule;
use yii\base\InvalidParamException;

class DateFilterRule extends BaseFilterRule
{
	public function isStatic()
	{
		return false;
	}

	protected function _applyInternal(&$collection)
	{
		if(!in_array($this->operator, static::operators()))
		{
			$this->_throwInvalidOperatorException(static::className());
		}

		$collection->where([$this->operator, 'timestamp', $this->value]);
	}

	public static function operators()
	{
		return [
			'>',
			'<',
			'>=',
			'<=',
			'!=',
			'==',
		];
	}

	public static function rules()
	{
		return [
			['value', 'date'],
			['operator', 'app\components\filter\OperatorValidator', 'ruleClass' => static::className()],
		];
	}
}