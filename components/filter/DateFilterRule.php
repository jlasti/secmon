<?php
namespace app\components\filter;

use app\components\filter\BaseFilterRule;
use yii\base\InvalidParamException;

class DateFilterRule extends BaseFilterRule
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
				$collection->orWhere([$this->operator, 'datetime', $this->value]);
				break;
			case 'AND':
			default:
				$collection->andWhere([$this->operator, 'datetime', $this->value]);
				break;
		}
	}

	/**
	 * @inheritdoc
	 */
	public static function operators()
	{
		return [
			'>',
			'<',
			'>=',
			'<=',
			'!=',
			'=',
		];
	}

    /**
     * @inheritdoc
     */
	public static function getValueType()
    {
        return FilterValueTypeEnum::DATE;
    }

    /**
	 * @inheritdoc
	 */
	public static function rules()
	{
		return array_merge(parent::rules(), [
			['value', 'date', 'format' => 'yyyy-MM-dd'],
		]);
	}
}