<?php

namespace app\models;

use app\components\filter\FilterQuery;
use app\components\filter\FilterTypeEnum;
use Yii;
use yii\base\InvalidConfigException;

/**
 * This is the model class for table "events_normalized".
 *
 * @property string $id
 * @property string $datetime
 * @property string $host
 * @property string $cef_version
 * @property string $cef_vendor
 * @property string $cef_dev_prod
 * @property string $cef_dev_version
 * @property integer $cef_event_class_id
 * @property string $cef_name
 * @property integer $cef_severity
 * @property string $src_ip
 * @property string $dst_ip
 * @property integer $src_port
 * @property integer $dst_port
 * @property string $protocol
 * @property string $src_mac
 * @property string $dst_mac
 * @property string $src_country
 * @property string $dst_country
 * @property string $src_city
 * @property string $dst_city
 * @property float $src_latitude
 * @property float $dst_latitude
 * @property float $src_longtitude
 * @property float $dst_longtitude
 * @property string $extensions
 * @property string $raw
 * @property boolean analyzed
 */
class EventsNormalized extends BaseEvent
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
            [['cef_version', 'cef_vendor', 'cef_dev_prod', 'cef_dev_version', 'cef_event_class_id', 'cef_name', 'cef_severity'], 'required'],
            [['cef_event_class_id', 'cef_severity', 'src_port', 'dst_port'], 'integer'],
            [['extensions', 'raw'], 'string'],
            [['analyzed'], 'boolean'],
            [['src_latitude', 'dst_latitude', 'src_longitude', 'dst_longitude'], 'double'],
            [['host', 'cef_version', 'cef_vendor', 'cef_dev_prod', 'cef_dev_version', 'cef_name', 'src_ip', 'dst_ip', 'protocol', 'src_mac', 'dst_mac', 'src_country', 'dst_country', 'src_city', 'dst_city', 'src_latitude', 'dst_latitude', 'src_longitude', 'dst_longitude'], 'string', 'max' => 255],
        ];
    }

    public static function columns()
    {
        return [
            'id' => [ FilterTypeEnum::COMPARE ],
            'datetime' => [ FilterTypeEnum::DATE ],
            'host' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'cef_version' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'cef_vendor' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'cef_dev_prod' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'cef_dev_version' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'cef_event_class_id' => [ FilterTypeEnum::COMPARE ],
            'cef_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'cef_severity' => [ FilterTypeEnum::COMPARE ],
            'src_ip' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'dst_ip' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'src_port' => [ FilterTypeEnum::COMPARE ],
            'dst_port' => [ FilterTypeEnum::COMPARE ],
            'protocol' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'src_mac' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'dst_mac' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'src_country' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'dst_country' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'src_city' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'dst_city' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'src_latitude' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'dst_latitude' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'src_longitude' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'dst_longitude' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'extensions' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'raw' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
        ];
    }

    public static function labels()
    {
        return [
            'id' => 'ID',
            'datetime' => 'Datetime',
            'host' => 'Host',
            'cef_version' => 'Cef Version',
            'cef_vendor' => 'Cef Vendor',
            'cef_dev_prod' => 'Cef Dev Prod',
            'cef_dev_version' => 'Cef Dev Version',
            'cef_event_class_id' => 'Cef Event Class ID',
            'cef_name' => 'Cef Name',
            'cef_severity' => 'Cef Severity',
            'src_ip' => 'Src Ip',
            'dst_ip' => 'Dst Ip',
            'src_port' => 'Src Port',
            'dst_port' => 'Dst Port',
            'protocol' => 'Protocol',
            'src_mac' => 'Src Mac',
            'dst_mac' => 'Dst Mac',
            'src_country' => 'Src Country',
            'dst_country' => 'Dst Country',
            'src_city' => 'Src City',
            'dst_city' => 'Dst City',
            'src_latitude' => 'Src Latitude',
            'dst_latitude' => 'Dst Latitude',
            'src_longitude' => 'Src Longitude',
            'dst_longitude' => 'Dst Longitude',
            'extensions' => 'Extensions',
            'raw' => 'Raw',
        ];
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
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
