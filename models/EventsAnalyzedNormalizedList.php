<?php

namespace app\models;

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
