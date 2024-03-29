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
 * @property string|null $time_filter_type
 * @property string|null $refresh_time
 * @property int|null number_of_records
 * @property string|null $data_columns
 * 
 *
 * @property Filter $filter
 * @property Users $user
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
            [['number_of_records'], 'default', 'value' => 10],
            [['user_id', 'filter_id', 'time_filter_id'], 'default', 'value' => null],
            [['user_id', 'filter_id', 'time_filter_id', 'number_of_records'], 'integer'],
            [['time_filter_type'], 'default', 'value' => 'absolute'],
            [['time_filter_type', 'refresh_time'], 'string'],
            [['refresh_time'], 'match', 'pattern' => '/^\d{1,5}[YMWDHmS]{1}$/',
            'message' => 'Enter valid format(nY/nM/nW/nD/nH/nm/nS)!'],
            [['filter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filter::class, 'targetAttribute' => ['filter_id' => 'id']],
            [['time_filter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filter::class, 'targetAttribute' => ['time_filter_id' => 'id']],
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
            'time_filter_type' => 'Time Filter Type',
            'refresh_time' => 'Refresh Time',
            'number_of_records' => 'Number of Records',
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
        return $this->hasOne(Filters::class, ['id' => 'time_filter_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
