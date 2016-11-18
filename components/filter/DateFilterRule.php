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
		$collection->andWhere([$this->operator, 'timestamp', $this->value]);
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
        return FilterValueTypeEnum::Date;
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