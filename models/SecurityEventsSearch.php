<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SecurityEvents;

/**
 * SecurityEventsSearch represents the model behind the search form of `app\models\SecurityEvents`.
 */
class SecurityEventsSearch extends SecurityEvents
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cef_severity', 'device_custom_number1', 'device_custom_number2', 'device_custom_number3', 'baseEventCount', 'device_direction', 'device_process_id', 'destination_translated_port', 'destination_process_id', 'destination_port', 'file_size', 'old_file_size', 'bytes_in', 'bytes_out', 'source_translated_port', 'source_process_id', 'source_port', 'agent_translated_zone_key', 'agent_zone_key', 'customer_key', 'destination_translated_zone_key', 'destination_zone_key', 'device_translated_zone_key', 'device_zone_key', 'source_translated_zone_key', 'source_zone_key', 'reported_duration', 'destination_ip_network_model', 'source_ip_network_model'], 'integer'],
            [['datetime', 'type', 'cef_version', 'cef_event_class_id', 'cef_device_product', 'cef_vendor', 'cef_device_version', 'cef_name', 'device_action', 'application_protocol', 'device_custom_ipv6_address1', 'device_custom_ipv6_address1_label', 'device_custom_ipv6_address2', 'device_custom_ipv6_address2_label', 'device_custom_ipv6_address3', 'device_custom_ipv6_address3_label', 'device_custom_ipv6_address4', 'device_custom_ipv6_address4_label', 'device_event_category', 'device_custom_floating_point1_label', 'device_custom_floating_point2_label', 'device_custom_floating_point3_label', 'device_custom_floating_point4_label', 'device_custom_number1_label', 'device_custom_number2_label', 'device_custom_number3_label', 'device_custom_string1', 'device_custom_string1_label', 'device_custom_string2', 'device_custom_string2_label', 'device_custom_string3', 'device_custom_string3_label', 'device_custom_string4', 'device_custom_string4_label', 'device_custom_string5', 'device_custom_string5_label', 'device_custom_string6', 'device_custom_string6_label', 'device_custom_date1', 'device_custom_date1_label', 'device_custom_date2', 'device_custom_date2_label', 'device_dns_domain', 'device_external_id', 'device_facility', 'device_inbound_interface', 'device_nt_domain', 'device_outbound_interface', 'device_payload_id', 'device_process_name', 'device_translated_address', 'device_time_zone', 'device_address', 'device_host_name', 'device_mac_address', 'destination_host_name', 'destination_mac_address', 'destination_nt_domain', 'destination_dns_domain', 'destination_service_name', 'destination_translated_address', 'destination_user_privileges', 'destination_process_name', 'destination_address', 'destination_user_id', 'destination_user_name', 'destination_group_id', 'destination_group_name', 'end_time', 'external_id', 'file_create_time', 'file_hash', 'file_id', 'file_modification_time', 'file_name', 'file_path', 'file_permission', 'file_type', 'old_file_create_time', 'old_file_hash', 'old_file_id', 'old_file_modification_time', 'old_file_name', 'old_file_path', 'old_file_permission', 'old_file_type', 'flex_date1', 'flex_date1_label', 'flex_string1', 'flex_string1_label', 'flex_string2', 'flex_string2_label', 'message', 'event_outcome', 'transport_protocol', 'reason', 'request_url', 'request_client_application', 'request_context', 'request_cookies', 'request_method', 'device_receipt_time', 'source_host_name', 'source_mac_address', 'source_nt_domain', 'source_dns_domain', 'source_service_name', 'source_translated_address', 'source_user_privileges', 'source_process_name', 'source_address', 'source_user_id', 'source_user_name', 'source_group_id', 'source_group_name', 'start_time', 'reported_resource_group_name', 'reported_resource_id', 'reported_resource_name', 'reported_resource_type', 'framework_name', 'threat_actor', 'threat_attack_id', 'attack_type', 'source_country', 'source_city', 'destination_country', 'destination_city', 'source_code', 'destination_code', 'parent_events', 'cef_extensions', 'raw_event'], 'safe'],
            [['device_custom_floating_point1', 'device_custom_floating_point2', 'device_custom_floating_point3', 'device_custom_floating_point4', 'source_geo_longitude', 'source_geo_latitude', 'destination_geo_longitude', 'destination_geo_latitude'], 'number'],
            [['analyzed'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with selected filter and search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $filterId, $timeFilterId, $numberOfRecords)
    {
        $query = SecurityEvents::find();
        $filter = Filter::findOne(['id' => $filterId]);
        $timeFilter = Filter::findOne(['id' => $timeFilterId]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $numberOfRecords,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!empty($filter)) {
            $query->applyFilter($filter);
        }

        if (!empty($timeFilter)) {
            $query->applyFilter($timeFilter);
        }
        
        $query->limit(10)->all();

        Yii::$app->cache->flush();

        return $dataProvider;
    }
}
