<?php
namespace app\components\filter;

class CompareFilterRule extends BaseFilterRule
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
	public static  function type()
    {
        return FilterTypeEnum::COMPARE;
    }

    /**
     * @inheritdoc
     */
    public static  function title()
    {
        return 'Compare';
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

    /**
     * @inheritdoc
     */
    protected function _applyInternal(&$collection)
    {
        switch($this->logic_operator)
        {
            case 'OR':
                $collection->orWhere([$this->operator, $this->column, $this->value]);
                break;
            case 'AND':
            default:
                $collection->andWhere([$this->operator, $this->column, $this->value]);
                break;
        }
    }
}