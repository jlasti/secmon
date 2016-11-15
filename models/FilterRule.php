<?php

namespace app\models;

use app\components\filter\BaseFilterRule;
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
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'filter_rules';
    }

	/**
	 * Returns available filter types in associative format ['type' => 'type name']
	 *
	 * @return array
	 */
    public static function types()
	{
		return [
			'date' => 'Date',
			'type' => 'Type',
			'regex' => 'Regular expression',
		];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
        	$this->type ? call_user_func($this->_getRuleClass() . '::rules') : [],
        	[
				[['filter_id'], 'integer'],
				[['value'], 'string'],
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
