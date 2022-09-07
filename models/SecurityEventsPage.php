<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "security_events_page".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $filter_id
 * @property int|null $time_filter_id
 * @property string|null $refresh_time
 * @property string|null $data_columns
 *
 * @property Filters $filter
 * @property TimeFilters $timeFilter
 * @property User $user
 */
class SecurityEventsPage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'security_events_page';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'filter_id', 'time_filter_id'], 'default', 'value' => null],
            [['user_id', 'filter_id', 'time_filter_id'], 'integer'],
            [['refresh_time', 'data_columns'], 'string'],
            [['filter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filters::class, 'targetAttribute' => ['filter_id' => 'id']],
            [['time_filter_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimeFilters::class, 'targetAttribute' => ['time_filter_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'filter_id' => 'Filter ID',
            'time_filter_id' => 'Time Filter ID',
            'refresh_time' => 'Refresh Time',
            'data_columns' => 'Data Columns',
        ];
    }

    /**
     * Gets query for [[Filter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFilter()
    {
        return $this->hasOne(Filters::class, ['id' => 'filter_id']);
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

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }
}
