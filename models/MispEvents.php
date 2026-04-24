<?php
namespace app\models;

use Yii;
use app\components\filter\FilterQuery;
use app\components\filter\FilterTypeEnum;
use yii\db\ActiveRecord;
use yii\base\InvalidConfigException;

class MispEvents extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%misp_events}}';
    }

    public function rules()
    {
        return [
            [['is_sent'], 'required'],
            [['threat_level', 'analysis', 'timestamp'], 'integer'],
            [['creator_org'], 'string'],
            [['tags'], 'safe'],
            [['event_uuid'], 'string', 'max' => 36],
            [['is_sent'], 'boolean'],
            [['sent_at', 'created_at'], 'safe'],
        ];
    }

    public function getMispAttributes()
    {
        return $this->hasMany(MispAttributes::class, ['event_id' => 'event_id']);
    }

    public static function columns()
    {
        return [
            'event_id' => [FilterTypeEnum::COMPARE],
            'event_uuid' => [FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE],
            'creator_org' => [FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE],
            'threat_level' => [FilterTypeEnum::COMPARE],
            'analysis' => [FilterTypeEnum::COMPARE],
            'timestamp' => [FilterTypeEnum::DATE],
            'tags' => [FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE],
            'created_at' => [FilterTypeEnum::DATE],
            'is_sent' => [FilterTypeEnum::COMPARE],
            'sent_at' => [FilterTypeEnum::DATE],
        ];
    }

    public static function labels()
    {
        return [
            'event_id' => 'Event ID',
            'event_uuid' => 'Event UUID',
            'creator_org' => 'Creator organization',
            'threat_level' => 'Threat level',
            'analysis' => 'Analysis',
            'timestamp' => 'Timestamp',
            'tags' => 'Tags',
            'created_at' => 'Created in SecMon',
            'is_sent' => 'Sent to MISP',
            'sent_at' => 'Sent at',
        ];
    }

    public static function find()
    {
        try {
            return Yii::createObject(FilterQuery::class, [get_called_class()]);
        } catch (InvalidConfigException $e) {
            echo 'Message: ' . $e->getMessage();
        }
    }
}