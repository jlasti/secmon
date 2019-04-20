<?php

namespace app\models;

use app\components\filter\FilterQuery;
use app\components\filter\FilterTypeEnum;
use Yii;
use yii\base\InvalidConfigException;

/**
 * This is the model class for table "clustered_events".
 *
 * @property integer $id
 * @property integer $time
 * @property string $raw
 * @property integer $cluster_number
 * @property integer $cluster_run
 */
class EventsClustered extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clustered_events';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time'], 'safe'],
            [['cluster_run', 'cluster_number'], 'integer'],
            [['raw'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public static function columns()
    {
        return [
            'time' => [ FilterTypeEnum::DATE ],
            'cluster_number' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'cluster_run' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'raw' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'time' => 'Datetime',
            'cluster_number' => 'Cluster',
            'cluster_run' => 'Cluster run',
            'raw' => 'Log',
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
