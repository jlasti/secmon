<?php

namespace app\models;

use app\components\filter\FilterQuery;
use app\components\filter\FilterTypeEnum;
use Yii;
use yii\base\InvalidConfigException;

/**
 * This is the model class for table "clustered_events_runs_clusters".
 *
 * @property integer $cluster_id
 */
class EventsClusteredFilteredClusters extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clustered_events_clusters';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'fk_run_id', 'number_of_events'], 'integer'],
            ['severity', 'integer', 'min' => 1, 'max' => 10],
            [['comment'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public static function columns()
    {
        return [
            'fk_run_id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'severity' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'comment' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'number_of_events' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fk_run_id' => 'Run Id',
            'id' => 'Cluster Id',
            'severity' => 'Cluster Severity',
            'comment' => 'Comment',
            'number_of_events' => 'Number of Events',
        ];
    }

    /**
     * @return object
     */
    public static function find()
    {
        try {
            return Yii::createObject(FilterQuery::className(), [get_called_class()]);
        } catch (InvalidConfigException $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }
}
