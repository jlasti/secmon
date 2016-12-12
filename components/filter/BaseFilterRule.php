<?php
namespace app\components\filter;

use yii\base\Component;
use yii\base\InvalidParamException;

abstract class BaseFilterRule extends Component
{
	//region Constants
	const EVENT_BEFORE_APPLY = 'before_apply';
	const EVENT_AFTER_APPLY = 'after_apply';
	//endregion

	//region Private attributes
	private $_collection;
    private $_operatorsDropdown = null;
	//endregion

	//region Public attributes
	public $value;
	public $operator;
	//endregion

	//region Constructor and init
	public function init()
	{
		$this->on(self::EVENT_BEFORE_APPLY, [$this, '_checkCollectionOrThrow']);
		$this->on(self::EVENT_BEFORE_APPLY, [$this, '_checkOperator']);
	}
	//endregion

	//region Public methods
	/**
	 * Determines if filter is applied to query or returned collection.
	 * If is static, it is applied after getting results.
	 *
	 * @return boolean
	 */
	public abstract function isStatic();

	/**
	 * Returns allowed operators for specific rule.
	 *
	 * @return array
	 */
	public static abstract function operators();

    /**
     * Return combined array of operators.
     *
     * @return array
     */
    public function getOperatorsForDropdown()
    {
        if ($this->_operatorsDropdown == null)
            $this->_operatorsDropdown = array_combine($this->operators(), $this->operators());

        return $this->_operatorsDropdown;
    }

    /**
     * Returns type of value. View input value field can be changed by this value.
     *
     * @return int
     */
    public static function getValueType()
    {
        return FilterValueTypeEnum::STRING;
    }

    /**
     * Returns logical operators for filter rules
     *
     * @return array
     */
    public static function getLogicalOperators()
    {
        return [
            'OR',
            'AND'
        ];
    }


        /**
	 * Validation rules applied to \app\models\FilterRule class
	 *
	 * @return array
	 */
	public static function rules()
	{
		return [
			['operator', 'app\components\filter\OperatorValidator', 'ruleClass' => static::className()],
		];
	}

	/**
	 * Applies rule to query or models collection.
	 *
	 * @param FilterQuery|\app\models\Event[] $collection
	 */
	public function apply(&$collection)
	{
		$this->_collection = $collection;

		$this->trigger(self::EVENT_BEFORE_APPLY);

		$this->_applyInternal($collection);

		$this->trigger(self::EVENT_AFTER_APPLY);
	}
	//endregion

	//region Protected methods
	/**
	 * Rule-specific implementation for apply
	 *
	 * @see self::apply
	 */
	protected abstract function _applyInternal(&$collection);

	/**
	 * Checks if specified operator is allowed
	 *
	 * @throws InvalidParamException
	 */
	protected function _checkOperator()
	{
		if(!in_array($this->operator, static::operators()))
		{
			throw new InvalidParamException(sprintf('Operator %s not allowed in class %s', $this->operator, static::className()));
		}
	}

	/**
	 * Checks if collection, which is this rule being applied to is valid object (FilterQuery or Event array)
	 *
	 * @throws InvalidParamException
	 */
	protected function _checkCollectionOrThrow()
	{
		if(!$this->isStatic() && !($this->_collection instanceof FilterQuery))
		{
			throw new InvalidParamException('Parameter $collection must be instance of \yii\db\Query.');
		}
		elseif($this->isStatic() && !is_array($this->_collection))
		{
			throw new InvalidParamException('Parameter $collection must be an array.');
		}
	}
	//endregion
}