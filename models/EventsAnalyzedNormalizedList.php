<?php

namespace app\models;

use app\components\filter\FilterTypeEnum;

/**
 * This is the model class for table "analyzed_normalized_events_list".
 *
 * @property integer $id
 * @property integer $events_analyzed_id
 * @property integer $events_analyzed_iteration
 * @property integer $events_analyzed_normalized_id
 */
class EventsAnalyzedNormalizedList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'analyzed_normalized_events_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['events_analyzed_normalized_id'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public static function columns()
    {
        return [
            'events_analyzed_normalized_id' => [ FilterTypeEnum::COMPARE ],
            'datetime' => [ FilterTypeEnum::DATE ],
            'eventNormalized.host' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventNormalized.cef_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventNormalized.src_ip' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventNormalized.dst_ip' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventNormalized.src_country' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventNormalized.dst_country' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventNormalized.src_code' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventNormalized.dst_code' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'events_analyzed_normalized_id' => 'ID analyzed normalized event',
            'eventNormalized.datetime' => 'Date',
            'eventNormalized.cef_name' => 'Cef Name',
            'eventNormalized.host' => 'Host',
            'eventNormalized.src_ip' => 'Src IP',
            'eventNormalized.dst_ip' => 'Dst IP',
            'eventNormalized.src_country' => 'Src Country',
            'eventNormalized.dst_country' => 'Dst Country',
            'eventNormalized.src_code' => 'Src Country Code',
            'eventNormalized.dst_code' => 'Dst Country Code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventNormalized(){
        return $this->hasOne(EventsNormalized::className(),['id'=>'id']);
    }
}
