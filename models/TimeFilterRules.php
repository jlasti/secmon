<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "time_filter_rules".
 *
 * @property int $id
 * @property int|null $time_filter_id
 * @property string|null $type
 * @property string|null $value
 * @property string|null $operator
 * @property string|null $logic_operator
 * @property int|null $position
 * @property string|null $column
 *
 * @property TimeFilters $timeFilter
 */
class TimeFilterRules extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'time_filter_rules';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['time_filter_id', 'position'], 'default', 'value' => null],
            [['time_filter_id', 'position'], 'integer'],
            [['type', 'column'], 'safe'],
            [['value'], 'string'],
            [['operator', 'logic_operator'], 'string', 'max' => 255],
            [['time_filter_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimeFilters::class, 'targetAttribute' => ['time_filter_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time_filter_id' => 'Time Filter ID',
            'type' => 'Type',
            'value' => 'Value',
            'operator' => 'Operator',
            'logic_operator' => 'Logic Operator',
            'position' => 'Position',
            'column' => 'Column',
        ];
    }

    /**
     * Gets query for [[TimeFilter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimeFilter()
    {
        return $this->hasOne(TimeFilters::class, ['id' => 'time_filter_id']);
    }
}
