<?php
namespace app\models;

use Yii;
use app\components\filter\FilterQuery;
use app\components\filter\FilterTypeEnum;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeTypecastBehavior;
use yii\base\InvalidConfigException;

class MispAttributes extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%misp_attributes}}';
    }

    public function rules()
    {
        return [
            [['event_id', 'value'], 'required'],
            [['event_id', 'timestamp'], 'integer'],
            [['to_ids', 'disable_correlation'], 'boolean'],
            [['tags', 'galaxy_clusters'], 'safe'],
            [['attribute_uuid', 'category', 'type', 'object_relation'], 'string', 'max' => 100],
            [['value'], 'string', 'max' => 191],
            [['comment'], 'string'],
            [['first_seen', 'last_seen'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'tags' => AttributeTypecastBehavior::TYPE_STRING,
                    'galaxy_clusters' => AttributeTypecastBehavior::TYPE_STRING,
                ],
                'typecastAfterFind' => true,
                'typecastBeforeSave' => true,
            ],
        ];
    }

    // Relation to parent MISP event
    public function getMispEvent()
    {
        return $this->hasOne(MispEvents::class, ['event_id' => 'event_id']);
    }

    // Get event UUID from related MISP event
    public function getEventUuid()
    {
        return $this->mispEvent->event_uuid ?? null;
    }

    // Fetch attribute by ID or return empty instance
    public static function getMispAttribute($id)
    {
        if ($id && ($model = self::findOne($id)) !== null) {
            return $model;
        }
        return new self();
    }

    public static function columns()
    {
        return [
            'attribute_id' => [FilterTypeEnum::COMPARE],
            'attribute_uuid' => [FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE],
            'event_id' => [FilterTypeEnum::COMPARE],
            'category' => [FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE],
            'type' => [FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE],
            'value' => [FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE],
            'to_ids' => [FilterTypeEnum::COMPARE],
            'comment' => [FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE],
            'first_seen' => [FilterTypeEnum::DATE],
            'last_seen' => [FilterTypeEnum::DATE],
            'disable_correlation' => [FilterTypeEnum::COMPARE],
            'object_relation' => [FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE],
            'timestamp' => [FilterTypeEnum::DATE],
        ];
    }

    public static function labels()
    {
        return [
            'attribute_id' => 'Attribute ID',
            'attribute_uuid' => 'Attribute UUID',
            'event_id' => 'Event ID',
            'category' => 'Category',
            'type' => 'Type',
            'value' => 'Value',
            'to_ids' => 'IDS',
            'tags' => 'Tags',
            'timestamp' => 'Timestamp',
            'comment' => 'Comment',
            'first_seen' => 'First seen',
            'last_seen' => 'Last seen',
            'disable_correlation' => 'Disable correlation',
            'object_relation' => 'Object relation',
            'galaxy_clusters' => 'Galaxy clusters',
            'eventUuid' => 'MISP Event UUID',
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