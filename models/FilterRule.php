<?php

namespace app\models;

use Yii;

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
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
        	call_user_func($this->_getRuleClass() . '::rules'),
        	[
				[['filter_id'], 'integer'],
				[['value'], 'string'],
				[['type', 'operator'], 'string', 'max' => 255],
				[['filter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filter::className(), 'targetAttribute' => ['filter_id' => 'id']],
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

    public function getRule()
	{
		$params = [
			'class' => $this->_getRuleClass(),
			'operator' => $this->operator,
			'value' => $this->value,
		];

		return Yii::createObject($params);
	}

	protected function _getRuleClass()
	{
		return sprintf('app\components\filter\%sFilterRule', ucfirst($this->type));
	}
}
