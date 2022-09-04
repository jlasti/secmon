<?php

namespace app\models;

use app\models\SecurityEvents;
use app\components\filter\FilterQuery;
use app\components\filter\FilterTypeEnum;
use Yii;
use yii\base\InvalidConfigException;

/**
 * This is the model class for table "events_clustered_events".
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
        return 'security_events';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datetime'], 'safe'],
            [['device_host_name', 'cef_name', 'source_address', 'destination_address', 'application_protocol'], 'string', 'max' => 255],
            [['id', 'cef_severity', 'source_port', 'destination_port'], 'integer'],
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
            'device_host_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'cef_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'cef_severity' => [ FilterTypeEnum::COMPARE ],
            'source_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'destination_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'source_port' => [ FilterTypeEnum::COMPARE ],
            'destination_port' => [ FilterTypeEnum::COMPARE ],
            'application_protocol' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
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
            'device_host_name' => 'Device Host Name',
            'cef_name' => 'Cef Name',
            'cef_severity' => 'Cef Severity',
            'source_address' => 'Source Address',
            'destination_address' => 'Destination Address',
            'source_port' => 'Source Port',
            'destination_port' => 'Destination Port',
            'application_protocol' => 'Application Protocol',
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
