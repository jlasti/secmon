<?php

namespace app\models;

use app\models\EventsNormalized;
use app\components\filter\FilterQuery;
use app\components\filter\FilterTypeEnum;
use Yii;
use yii\base\InvalidConfigException;

/**
 * This is the model class for table "clustered_events_runs_clusters".
 *
 * @property integer $cluster_id
 */
class EventsClusteredEvents extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'events_normalized';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datetime'], 'safe'],
            [['host', 'cef_name', 'src_ip', 'dst_ip', 'protocol'], 'string', 'max' => 255],
            [['id', 'cef_severity'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public static function columns()
    {
        return [
            'id' => [ FilterTypeEnum::COMPARE ],
            'datetime' => [ FilterTypeEnum::DATE ],
            'host' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'cef_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'cef_severity' => [ FilterTypeEnum::COMPARE ],
            'src_ip' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'dst_ip' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'src_port' => [ FilterTypeEnum::COMPARE ],
            'dst_port' => [ FilterTypeEnum::COMPARE ],
            'protocol' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'datetime' => 'Datetime',
            'host' => 'Host',
            'cef_name' => 'Cef Name',
            'cef_severity' => 'Cef Severity',
            'src_ip' => 'Src Ip',
            'dst_ip' => 'Dst Ip',
            'src_port' => 'Src Port',
            'dst_port' => 'Dst Port',
            'protocol' => 'Protocol',
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
