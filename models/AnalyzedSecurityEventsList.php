<?php

namespace app\models;

use app\components\filter\FilterTypeEnum;

/**
 * This is the model class for table "analyzed_security_events_list".
 *
 * @property integer $id
 * @property integer $security_events_id
 * @property integer $events_analyzed_iteration
 * @property integer $analyzed_security_events_id
 */
class AnalyzedSecurityEventsList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'analyzed_security_events_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['analyzed_security_events_id'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public static function columns()
    {
        return [
            'analyzed_security_events_id' => [ FilterTypeEnum::COMPARE ],
            'datetime' => [ FilterTypeEnum::DATE ],
            'eventSecurity.device_host_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventSecurity.cef_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventSecurity.source_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventSecurity.destination_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventSecurity.source_country' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventSecurity.destination_country' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventSecurity.source_code' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'eventSecurity.destination_code' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'analyzed_security_events_id' => 'ID analyzed security event',
            'eventSecurity.datetime' => 'Date',
            'eventSecurity.cef_name' => 'Cef Name',
            'eventSecurity.device_host_name' => 'Host',
            'eventSecurity.source_address' => 'Src IP',
            'eventSecurity.destination_address' => 'Dst IP',
            'eventSecurity.source_country' => 'Src Country',
            'eventSecurity.destination_country' => 'Dst Country',
            'eventSecurity.source_code' => 'Src Country Code',
            'eventSecurity.destination_code' => 'Dst Country Code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecurityEvent(){
        return $this->hasOne(SecurityEvents::className(),['id'=>'id']);
    }
}
