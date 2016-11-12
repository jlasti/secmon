<?php
namespace app\components\filter;

use yii\base\Component;
use yii\base\InvalidParamException;

abstract class BaseFilterRule extends Component
{
	const EVENT_BEFORE_APPLY = 'before_apply';
	const EVENT_AFTER_APPLY = 'after_apply';

	private $_collection;

	public $value;
	public $operator;

	public function init()
	{
		$this->on(self::EVENT_BEFORE_APPLY, [$this, '_checkCollectionOrThrow']);
	}

	public abstract function isStatic();

	public static abstract function operators();

	public static abstract function rules();

	public function apply(&$collection)
	{
		$this->_collection = $collection;

		$this->trigger(self::EVENT_BEFORE_APPLY);

		$this->_applyInternal($collection);

		$this->trigger(self::EVENT_AFTER_APPLY);
	}

	protected abstract function _applyInternal(&$collection);

	protected function _throwInvalidOperatorException($class)
	{
		throw new InvalidParamException(sprintf('Operator %s not allowed in class %s', $this->operator, $class));
	}

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
}