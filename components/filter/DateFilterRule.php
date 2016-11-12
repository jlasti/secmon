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

	public function apply(&$collection)
	{
		if(!($collection instanceof FilterQuery))
		{
			throw new InvalidParamException('Parameter $collection must be instance of \yii\db\Query');
		}

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