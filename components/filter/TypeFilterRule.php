<?php
namespace app\components\filter;

class TypeFilterRule extends BaseFilterRule
{
	/**
	 * @inheritdoc
	 */
	public function isStatic()
	{
		return false;
	}

	/**
	 * @inheritdoc
	 */
	protected function _applyInternal(&$collection)
	{
		$collection->andWhere([$this->operator, 'type_id', $this->value]);
	}

	/**
	 * @inheritdoc
	 */
	public static function operators()
	{
		return [
			'=',
			'!=',
		];
	}
}