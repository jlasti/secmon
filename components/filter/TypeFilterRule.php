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
		switch($this->logic_operator)
		{
			case 'OR':
				$collection->orWhere([$this->operator, 'dst_port', $this->value]);
				break;
			case 'AND':
			default:
				$collection->andWhere([$this->operator, 'dst_port', $this->value]);
				break;
		}
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