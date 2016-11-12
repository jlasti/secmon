<?php
namespace app\components\filter;

class TypeFilterRule extends BaseFilterRule
{
	public function isStatic()
	{
		return false;
	}

	protected function _applyInternal(&$collection)
	{
		$collection->where([$this->operator, 'type_id', $this->value]);
	}

	public static function operators()
	{
		return [
			'=',
			'!=',
		];
	}

	public static function rules()
	{
		return [
			['operator', 'app\components\filter\OperatorValidator', 'ruleClass' => static::className()],
		];
	}
}