<?php

namespace app\models;

use app\components\filter\FilterQuery;
use app\components\filter\FilterTypeEnum;
use Yii;
use yii\base\InvalidConfigException;

/**
 * This is the model class for table "clustered_events_runs".
 *
 * @property integer $id
 * @property integer $datetime
 * @property integer $type_of_algorithm
 */
class EventsClusteredRuns extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clustered_events_runs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datetime'], 'safe'],
            [['id', 'number_of_clusters'], 'integer'],
            [['type_of_algorithm', 'comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public static function columns()
    {
        return [
            'datetime' => [ FilterTypeEnum::DATE ],
            'id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'type_of_algorithm' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'number_of_clusters' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'commnet' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'datetime' => 'Datetime',
            'id' => 'Cluster run',
            'type_of_algorithm' => 'Type of Algorithm',
            'number_of_clusters' => 'Number of Clusters',
            'comment' => 'Comment'
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
