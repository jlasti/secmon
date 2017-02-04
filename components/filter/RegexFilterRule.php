<?php
namespace app\components\filter;


class RegexFilterRule extends BaseFilterRule
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
	public static function operators()
	{
		return [
			'REGEXP',
			'NOT REGEXP',
		];
	}

    /**
	 * @inheritdoc
	 */
	protected function _applyInternal(&$collection)
	{
		switch($this->logic_operator)
		{
			case 'OR':
				$collection->orWhere([$this->operator, 'description', $this->value]);
				break;
			case 'AND':
			default:
				$collection->andWhere([$this->operator, 'description', $this->value]);
				break;
		}
	}
}