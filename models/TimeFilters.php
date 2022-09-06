<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "time_filters".
 *
 * @property int $id
 * @property bool|null $filter_type
 *
 * @property SecurityEventsPage[] $securityEventsPages
 * @property TimeFilterRules[] $timeFilterRules
 */
class TimeFilters extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'time_filters';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['filter_type'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filter_type' => 'Filter Type',
        ];
    }

    /**
     * Gets query for [[SecurityEventsPages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecurityEventsPages()
    {
        return $this->hasMany(SecurityEventsPage::class, ['time_filter_id' => 'id']);
    }

    /**
     * Gets query for [[TimeFilterRules]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimeFilterRules()
    {
        return $this->hasMany(TimeFilterRules::class, ['time_filter_id' => 'id']);
    }
}
