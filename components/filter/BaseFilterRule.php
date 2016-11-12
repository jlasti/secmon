<?php
namespace app\components\filter;

use yii\base\InvalidParamException;
use yii\base\Object;

abstract class BaseFilterRule extends Object
{
	public $value;
	public $operator;

	public abstract function isStatic();

	public abstract function apply(&$collection);

	public static abstract function operators();

	public static abstract function rules();

	protected function _throwInvalidOperatorException($class)
	{
		throw new InvalidParamException(sprintf('Operator %s not allowed in class %s', $this->operator, $class));
	}
}