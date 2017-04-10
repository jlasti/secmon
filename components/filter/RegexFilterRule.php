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

    public static function columns()
    {
        return [
            'host',
            'cef_dev_prod',
            'cef_name',
            'src_ip',
            'dst_ip',
            'protocol',
            'src_mac',
            'dst_mac',
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