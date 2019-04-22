<?php
namespace app\components\filter;

use app\models\Filter;
use yii\db\ActiveQuery;

class FilterQuery extends ActiveQuery
{
	protected $_rules = [
		'static' => [],
		'dynamic' => [],
	];

	/**
	 * Applies provided filter to this query
	 *
	 * @param Filter $filter Filter to be applied
	 *
	 * @return $this
	 */
	public function applyFilter(Filter $filter)
	{
		foreach($filter->rules as $rule)
		{
			$this->applyRule($rule->rule);
		}

		return $this;
	}

	/**
	 * Applies provided rule to this query
	 *
	 * @param BaseFilterRule $rule Rule to be applied
	 *
	 * @return $this
	 */
	public function applyRule(BaseFilterRule $rule)
	{
		$this->_rules[$rule->isStatic()? 'static' : 'dynamic'][] = $rule;

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function all($db = null)
	{
		foreach($this->_rules['dynamic'] as $rule)
		{
			$rule->apply($this);
		}

		$data = parent::all($db);

		foreach($this->_rules['static'] as $rule)
		{
			$rule->apply($data);
		}

		return $data;
	}
}