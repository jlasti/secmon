<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "security_events_page".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $filter_id
 * @property int|null $time_filter_id
 * @property bool|null $auto_refresh
 * @property string|null $refresh_time
 * @property string|null $data_columns
 *
 * @property Filter $filter
 * @property TimeFilter $timeFilter
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
            [['user_id', 'filter_id', 'time_filter_id'], 'default', 'value' => null],
            [['user_id', 'filter_id', 'time_filter_id'], 'integer'],
            [['auto_refresh'], 'boolean'],
            [['refresh_time'], 'match', 'pattern' => '/^\d{1,5}[YMWDHmS]{1}$/',
            'message' => 'Enter valid format(nY/nM/nW/nD/nH/nm/nS)!'],
            [['filter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filter::class, 'targetAttribute' => ['filter_id' => 'id']],
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
            'auto_refresh' => 'Auto Refresh',
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
        return $this->hasOne(TimeFilter::class, ['id' => 'time_filter_id']);
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

    public static function replaceColumns($rawDataColumns, $searchModel)
    {
        $replaceDataColumns = [];
        array_push($replaceDataColumns, ['class' => 'yii\grid\SerialColumn',]);

        foreach($rawDataColumns as $column)
        {
            switch ($column) {
                case 'cef_severity':
                    array_push($replaceDataColumns, [
                        'attribute' => 'cef_severity',
                        'value' => 'cef_severity',
                        'contentOptions' => function ($dataProvider, $key, $index, $column) {
                            $array = [
                                ['id' => '1', 'data' => '#00DBFF'],
                                ['id' => '2', 'data' => '#00DBFF'],
                                ['id' => '3', 'data' => '#00FF00'],
                                ['id' => '4', 'data' => '#00FF00'],
                                ['id' => '5', 'data' => '#FFFF00'],
                                ['id' => '6', 'data' => '#FFFF00'],
                                ['id' => '7', 'data' => '#CC5500'],
                                ['id' => '8', 'data' => '#CC5500'],
                                ['id' => '9', 'data' => '#FF0000'],
                                ['id' => '10', 'data' => '#FF0000'],
                            ];
                            if (0 < $dataProvider->cef_severity && $dataProvider->cef_severity < 11){
                                $map = ArrayHelper::map($array, 'id', 'data');
                                return ['style' => 'background-color:'.$map[$dataProvider->cef_severity]];
                            } else {
                                return ['style' => 'background-color:#FFFFFF'];
                            }
                        }
                    ]);
                    break;
                case 'datetime':
                    array_push($replaceDataColumns, [
                        'attribute' => 'datetime',
                        'value' => 'datetime',
                        'format' => 'raw',
                        'filter' => \macgyer\yii2materializecss\widgets\form\DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'datetime',
                            'clientOptions' => [
                                'format' => 'yyyy-mm-dd'
                            ]
                        ])
                    ]);
                    break;
                case 'analyzed':
                    array_push($replaceDataColumns, [
                        'class' => '\dosamigos\grid\columns\BooleanColumn',
                        'attribute' => 'analyzed',
                        'treatEmptyAsFalse' => true
                    ]);
                    break;
                default:
                    array_push($replaceDataColumns, $column);
            }
        }
        array_push($replaceDataColumns, ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn', 'template'=>'{view}']);

        return $replaceDataColumns;
    }
}
