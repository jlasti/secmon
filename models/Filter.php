<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "filters".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property boolean $time_filter
 *
 * @property FilterRule[] $filterRules
 * @property User $user
 */
class Filter extends \yii\db\ActiveRecord
{
	private $_rules;

	/**
	* @inheritdoc
	*/
	public static function tableName()
	{
		return 'filters';
	}

	/**
	* @inheritdoc
	*/
	public function rules()
	{
		return [
			['name', 'required'],
			[['user_id'], 'integer'],
			[['name'], 'string', 'max' => 255],
			[['time_filter'], 'default', 'value' => false],
			[['time_filter'], 'boolean'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
			['name', 'unique', 'targetAttribute' => ['name', 'user_id'], 'message' => 'Choosen filter name has already been taken.']
		];
	}

	/**
	* @inheritdoc
	*/
	public function attributeLabels()
	{
		return [
		    'id' => Yii::t('app', 'ID'),
		    'user_id' => Yii::t('app', 'User ID'),
		    'name' => Yii::t('app', 'Name'),
		];
	}

	/**
	* @return \yii\db\ActiveQuery
	*/
	public function getRules()
	{
		return $this->hasMany(FilterRule::className(), ['filter_id' => 'id'])->orderBy('position ASC');
	}

	/**
	* @return \yii\db\ActiveQuery
	*/
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

	/**
	 * Sets specified rules to filter
	 *
	 * @param FilterRule[] $rules
	 */
	public function setRules($rules)
	{
		$this->_rules = $rules;
	}

	/**
	 * @inheritdoc
	 */
	public function save($runValidation = true, $attributeNames = null)
	{
		$transaction = Yii::$app->db->beginTransaction();

		if(parent::save($runValidation, $attributeNames))
		{
		    $i = 0;

			foreach($this->_rules as $rule)
			{
				$rule->filter_id = $this->id;
				$rule->position = $i;

				if(!$rule->save())
				{
					$transaction->rollBack();

					return false;
				}

				$i++;
			}

			$transaction->commit();

			return true;
		}
		return false;
	}
}
