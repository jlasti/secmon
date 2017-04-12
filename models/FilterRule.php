<?php

namespace app\models;

use app\components\filter\BaseFilterRule;
use app\components\filter\DateFilterRule;
use app\components\filter\RegexFilterRule;
use app\components\filter\CompareFilterRule;
use Symfony\Component\Finder\Expression\Regex;
use Yii;
use yii\base\Model;

/**
 * This is the model class for table "filter_rules".
 *
 * @property integer $id
 * @property integer $filter_id
 * @property string $type
 * @property string $value
 * @property string $operator
 *
 * @property Filters $filter
 */
class FilterRule extends \yii\db\ActiveRecord
{
    private static $_types = null;
    private static $_logicalOperators = null;
    private static $_logicalOperatorsDropdown = null;
    private static $_typesDropdown = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'filter_rules';
    }

    /**
     * Returns available filter types
     *
     * @return array of BaseFilterRule
     */
    public static function types()
    {
        if (self::$_types == null)
        {
            self::$_types = [
                new CompareFilterRule(),
                new DateFilterRule(),
                new RegexFilterRule()
            ];
        }

        return self::$_types;
    }

    /**
     * Returns logical operators for filter rules
     *
     * @return array
     */
    public static function logicalOperators()
    {
        if (self::$_logicalOperators == null)
        {
            self::$_logicalOperators = [
                'OR',
                'AND'
            ];
        }

        return self::$_logicalOperators;
    }

    /**
     * Returns logical operators for dropdown
     *
     * @return array
     */
    public static function getLogicalOperatorsForDropdown()
    {
        if (self::$_logicalOperatorsDropdown == null)
        {
            $operators = self::logicalOperators();
            self::$_logicalOperatorsDropdown = array_combine($operators, $operators);
        }

        return self::$_logicalOperatorsDropdown;
    }

    /**
     * Returns types for dropdown
     *
     * @return array
     */
    public static function getTypesForDropdown()
    {
        if (self::$_typesDropdown == null)
        {
            $types = self::types();
            self::$_typesDropdown = array_combine(
                array_map(function($type) { return $type->type(); }, $types),
                array_map(function($type) { return $type->title(); }, $types));
        }

        return self::$_typesDropdown;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
        	$this->type ? call_user_func($this->_getRuleClass() . '::rules') : [],
        	[
            	['logic_operator', 'safe'],
				[['filter_id'], 'integer'],
				[['value', 'column'], 'string'],
				[['type', 'operator'], 'string', 'max' => 255],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'filter_id' => Yii::t('app', 'Filter ID'),
            'type' => Yii::t('app', 'Type'),
            'value' => Yii::t('app', 'Value'),
            'operator' => Yii::t('app', 'Operator'),
            'column' => Yii::t('app', 'Column'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilter()
    {
        return $this->hasOne(Filter::className(), ['id' => 'filter_id']);
    }

	/**
	 * Creates actual filter rule based on this model
	 *
	 * @return BaseFilterRule
	 */
    public function getRule()
	{
		$params = [
			'class' => $this->_getRuleClass(),
			'operator' => $this->operator,
			'value' => $this->value,
			'logic_operator' => $this->logic_operator,
            'column' => $this->column,
		];

		return Yii::createObject($params);
	}

	/**
	 * @inheritdoc
	 */
	public function validate($attributeNames = null, $clearErrors = true)
	{
		/**
		 * need to regenerate validators, because they are generated based on actual rule class
		 * so when updating model, it validates with previous rule type
		 */
		$reflection = new \ReflectionClass(get_class(new Model()));
		$validators = $reflection->getProperty('_validators');
		$validators->setAccessible(true);
		$validators->setValue($this, null);

		return parent::validate($attributeNames, $clearErrors);
	}

	/**
	 * Returns class name for actual rule
	 *
	 * @return string
	 */
	protected function _getRuleClass()
	{
		return sprintf('app\components\filter\%sFilterRule', ucfirst($this->type));
	}
}
