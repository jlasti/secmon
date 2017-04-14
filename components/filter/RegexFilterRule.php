<?php
namespace app\components\filter;

use Yii;

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
	public static  function type()
    {
        return FilterTypeEnum::REGEX;
    }

    /**
     * @inheritdoc
     */
    public static  function title()
    {
        return 'Regular expression';
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
				$collection->orWhere([$this->getDBOperator($this->operator), $this->column, $this->value]);
				break;
			case 'AND':
			default:
				$collection->andWhere([$this->getDBOperator($this->operator), $this->column, $this->value]);
				break;
		}
	}

	public function getDBOperator($operator)
    {
        $db = Yii::$app->db->getDriverName();
        $res = $operator;
        if ($db == 'pgsql')
        {
            if ($operator == 'REGEXP')
            {
                $res = 'LIKE';
            }
            else if ($operator == 'NOT REGEXP')
            {
                $res = 'NOT LIKE';
            }
        }
        return $res;
    }
}