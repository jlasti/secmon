<?php

namespace app\models;

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
    public function search($params, $filterId, $timeFilterId)
    {
        $query = SecurityEvents::find();
        $filter = Filter::findOne(['id' => $filterId]);
        $timeFilterId = Filter::findOne(['id' => $timeFilterId]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
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

        if (!empty($timeFilterId)) {
            $query->applyFilter($timeFilterId);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'datetime' => $this->datetime,
            'cef_severity' => $this->cef_severity,
            'device_custom_floating_point1' => $this->device_custom_floating_point1,
            'device_custom_floating_point2' => $this->device_custom_floating_point2,
            'device_custom_floating_point3' => $this->device_custom_floating_point3,
            'device_custom_floating_point4' => $this->device_custom_floating_point4,
            'device_custom_number1' => $this->device_custom_number1,
            'device_custom_number2' => $this->device_custom_number2,
            'device_custom_number3' => $this->device_custom_number3,
            'baseEventCount' => $this->baseEventCount,
            'device_custom_date1' => $this->device_custom_date1,
            'device_custom_date2' => $this->device_custom_date2,
            'device_direction' => $this->device_direction,
            'device_process_id' => $this->device_process_id,
            'destination_translated_port' => $this->destination_translated_port,
            'destination_process_id' => $this->destination_process_id,
            'destination_port' => $this->destination_port,
            'end_time' => $this->end_time,
            'file_create_time' => $this->file_create_time,
            'file_modification_time' => $this->file_modification_time,
            'file_size' => $this->file_size,
            'old_file_create_time' => $this->old_file_create_time,
            'old_file_modification_time' => $this->old_file_modification_time,
            'old_file_size' => $this->old_file_size,
            'flex_date1' => $this->flex_date1,
            'bytes_in' => $this->bytes_in,
            'bytes_out' => $this->bytes_out,
            'device_receipt_time' => $this->device_receipt_time,
            'source_translated_port' => $this->source_translated_port,
            'source_process_id' => $this->source_process_id,
            'source_port' => $this->source_port,
            'start_time' => $this->start_time,
            'agent_translated_zone_key' => $this->agent_translated_zone_key,
            'agent_zone_key' => $this->agent_zone_key,
            'customer_key' => $this->customer_key,
            'destination_translated_zone_key' => $this->destination_translated_zone_key,
            'destination_zone_key' => $this->destination_zone_key,
            'device_translated_zone_key' => $this->device_translated_zone_key,
            'device_zone_key' => $this->device_zone_key,
            'source_translated_zone_key' => $this->source_translated_zone_key,
            'source_zone_key' => $this->source_zone_key,
            'reported_duration' => $this->reported_duration,
            'source_geo_longitude' => $this->source_geo_longitude,
            'source_geo_latitude' => $this->source_geo_latitude,
            'destination_geo_longitude' => $this->destination_geo_longitude,
            'destination_geo_latitude' => $this->destination_geo_latitude,
            'destination_ip_network_model' => $this->destination_ip_network_model,
            'source_ip_network_model' => $this->source_ip_network_model,
            'analyzed' => $this->analyzed,
        ]);

        $query->andFilterWhere(['ilike', 'type', $this->type])
            ->andFilterWhere(['ilike', 'cef_version', $this->cef_version])
            ->andFilterWhere(['ilike', 'cef_event_class_id', $this->cef_event_class_id])
            ->andFilterWhere(['ilike', 'cef_device_product', $this->cef_device_product])
            ->andFilterWhere(['ilike', 'cef_vendor', $this->cef_vendor])
            ->andFilterWhere(['ilike', 'cef_device_version', $this->cef_device_version])
            ->andFilterWhere(['ilike', 'cef_name', $this->cef_name])
            ->andFilterWhere(['ilike', 'device_action', $this->device_action])
            ->andFilterWhere(['ilike', 'application_protocol', $this->application_protocol])
            ->andFilterWhere(['ilike', 'device_custom_ipv6_address1', $this->device_custom_ipv6_address1])
            ->andFilterWhere(['ilike', 'device_custom_ipv6_address1_label', $this->device_custom_ipv6_address1_label])
            ->andFilterWhere(['ilike', 'device_custom_ipv6_address2', $this->device_custom_ipv6_address2])
            ->andFilterWhere(['ilike', 'device_custom_ipv6_address2_label', $this->device_custom_ipv6_address2_label])
            ->andFilterWhere(['ilike', 'device_custom_ipv6_address3', $this->device_custom_ipv6_address3])
            ->andFilterWhere(['ilike', 'device_custom_ipv6_address3_label', $this->device_custom_ipv6_address3_label])
            ->andFilterWhere(['ilike', 'device_custom_ipv6_address4', $this->device_custom_ipv6_address4])
            ->andFilterWhere(['ilike', 'device_custom_ipv6_address4_label', $this->device_custom_ipv6_address4_label])
            ->andFilterWhere(['ilike', 'device_event_category', $this->device_event_category])
            ->andFilterWhere(['ilike', 'device_custom_floating_point1_label', $this->device_custom_floating_point1_label])
            ->andFilterWhere(['ilike', 'device_custom_floating_point2_label', $this->device_custom_floating_point2_label])
            ->andFilterWhere(['ilike', 'device_custom_floating_point3_label', $this->device_custom_floating_point3_label])
            ->andFilterWhere(['ilike', 'device_custom_floating_point4_label', $this->device_custom_floating_point4_label])
            ->andFilterWhere(['ilike', 'device_custom_number1_label', $this->device_custom_number1_label])
            ->andFilterWhere(['ilike', 'device_custom_number2_label', $this->device_custom_number2_label])
            ->andFilterWhere(['ilike', 'device_custom_number3_label', $this->device_custom_number3_label])
            ->andFilterWhere(['ilike', 'device_custom_string1', $this->device_custom_string1])
            ->andFilterWhere(['ilike', 'device_custom_string1_label', $this->device_custom_string1_label])
            ->andFilterWhere(['ilike', 'device_custom_string2', $this->device_custom_string2])
            ->andFilterWhere(['ilike', 'device_custom_string2_label', $this->device_custom_string2_label])
            ->andFilterWhere(['ilike', 'device_custom_string3', $this->device_custom_string3])
            ->andFilterWhere(['ilike', 'device_custom_string3_label', $this->device_custom_string3_label])
            ->andFilterWhere(['ilike', 'device_custom_string4', $this->device_custom_string4])
            ->andFilterWhere(['ilike', 'device_custom_string4_label', $this->device_custom_string4_label])
            ->andFilterWhere(['ilike', 'device_custom_string5', $this->device_custom_string5])
            ->andFilterWhere(['ilike', 'device_custom_string5_label', $this->device_custom_string5_label])
            ->andFilterWhere(['ilike', 'device_custom_string6', $this->device_custom_string6])
            ->andFilterWhere(['ilike', 'device_custom_string6_label', $this->device_custom_string6_label])
            ->andFilterWhere(['ilike', 'device_custom_date1_label', $this->device_custom_date1_label])
            ->andFilterWhere(['ilike', 'device_custom_date2_label', $this->device_custom_date2_label])
            ->andFilterWhere(['ilike', 'device_dns_domain', $this->device_dns_domain])
            ->andFilterWhere(['ilike', 'device_external_id', $this->device_external_id])
            ->andFilterWhere(['ilike', 'device_facility', $this->device_facility])
            ->andFilterWhere(['ilike', 'device_inbound_interface', $this->device_inbound_interface])
            ->andFilterWhere(['ilike', 'device_nt_domain', $this->device_nt_domain])
            ->andFilterWhere(['ilike', 'device_outbound_interface', $this->device_outbound_interface])
            ->andFilterWhere(['ilike', 'device_payload_id', $this->device_payload_id])
            ->andFilterWhere(['ilike', 'device_process_name', $this->device_process_name])
            ->andFilterWhere(['ilike', 'device_translated_address', $this->device_translated_address])
            ->andFilterWhere(['ilike', 'device_time_zone', $this->device_time_zone])
            ->andFilterWhere(['ilike', 'device_address', $this->device_address])
            ->andFilterWhere(['ilike', 'device_host_name', $this->device_host_name])
            ->andFilterWhere(['ilike', 'device_mac_address', $this->device_mac_address])
            ->andFilterWhere(['ilike', 'destination_host_name', $this->destination_host_name])
            ->andFilterWhere(['ilike', 'destination_mac_address', $this->destination_mac_address])
            ->andFilterWhere(['ilike', 'destination_nt_domain', $this->destination_nt_domain])
            ->andFilterWhere(['ilike', 'destination_dns_domain', $this->destination_dns_domain])
            ->andFilterWhere(['ilike', 'destination_service_name', $this->destination_service_name])
            ->andFilterWhere(['ilike', 'destination_translated_address', $this->destination_translated_address])
            ->andFilterWhere(['ilike', 'destination_user_privileges', $this->destination_user_privileges])
            ->andFilterWhere(['ilike', 'destination_process_name', $this->destination_process_name])
            ->andFilterWhere(['ilike', 'destination_address', $this->destination_address])
            ->andFilterWhere(['ilike', 'destination_user_id', $this->destination_user_id])
            ->andFilterWhere(['ilike', 'destination_user_name', $this->destination_user_name])
            ->andFilterWhere(['ilike', 'destination_group_id', $this->destination_group_id])
            ->andFilterWhere(['ilike', 'destination_group_name', $this->destination_group_name])
            ->andFilterWhere(['ilike', 'external_id', $this->external_id])
            ->andFilterWhere(['ilike', 'file_hash', $this->file_hash])
            ->andFilterWhere(['ilike', 'file_id', $this->file_id])
            ->andFilterWhere(['ilike', 'file_name', $this->file_name])
            ->andFilterWhere(['ilike', 'file_path', $this->file_path])
            ->andFilterWhere(['ilike', 'file_permission', $this->file_permission])
            ->andFilterWhere(['ilike', 'file_type', $this->file_type])
            ->andFilterWhere(['ilike', 'old_file_hash', $this->old_file_hash])
            ->andFilterWhere(['ilike', 'old_file_id', $this->old_file_id])
            ->andFilterWhere(['ilike', 'old_file_name', $this->old_file_name])
            ->andFilterWhere(['ilike', 'old_file_path', $this->old_file_path])
            ->andFilterWhere(['ilike', 'old_file_permission', $this->old_file_permission])
            ->andFilterWhere(['ilike', 'old_file_type', $this->old_file_type])
            ->andFilterWhere(['ilike', 'flex_date1_label', $this->flex_date1_label])
            ->andFilterWhere(['ilike', 'flex_string1', $this->flex_string1])
            ->andFilterWhere(['ilike', 'flex_string1_label', $this->flex_string1_label])
            ->andFilterWhere(['ilike', 'flex_string2', $this->flex_string2])
            ->andFilterWhere(['ilike', 'flex_string2_label', $this->flex_string2_label])
            ->andFilterWhere(['ilike', 'message', $this->message])
            ->andFilterWhere(['ilike', 'event_outcome', $this->event_outcome])
            ->andFilterWhere(['ilike', 'transport_protocol', $this->transport_protocol])
            ->andFilterWhere(['ilike', 'reason', $this->reason])
            ->andFilterWhere(['ilike', 'request_url', $this->request_url])
            ->andFilterWhere(['ilike', 'request_client_application', $this->request_client_application])
            ->andFilterWhere(['ilike', 'request_context', $this->request_context])
            ->andFilterWhere(['ilike', 'request_cookies', $this->request_cookies])
            ->andFilterWhere(['ilike', 'request_method', $this->request_method])
            ->andFilterWhere(['ilike', 'source_host_name', $this->source_host_name])
            ->andFilterWhere(['ilike', 'source_mac_address', $this->source_mac_address])
            ->andFilterWhere(['ilike', 'source_nt_domain', $this->source_nt_domain])
            ->andFilterWhere(['ilike', 'source_dns_domain', $this->source_dns_domain])
            ->andFilterWhere(['ilike', 'source_service_name', $this->source_service_name])
            ->andFilterWhere(['ilike', 'source_translated_address', $this->source_translated_address])
            ->andFilterWhere(['ilike', 'source_user_privileges', $this->source_user_privileges])
            ->andFilterWhere(['ilike', 'source_process_name', $this->source_process_name])
            ->andFilterWhere(['ilike', 'source_address', $this->source_address])
            ->andFilterWhere(['ilike', 'source_user_id', $this->source_user_id])
            ->andFilterWhere(['ilike', 'source_user_name', $this->source_user_name])
            ->andFilterWhere(['ilike', 'source_group_id', $this->source_group_id])
            ->andFilterWhere(['ilike', 'source_group_name', $this->source_group_name])
            ->andFilterWhere(['ilike', 'reported_resource_group_name', $this->reported_resource_group_name])
            ->andFilterWhere(['ilike', 'reported_resource_id', $this->reported_resource_id])
            ->andFilterWhere(['ilike', 'reported_resource_name', $this->reported_resource_name])
            ->andFilterWhere(['ilike', 'reported_resource_type', $this->reported_resource_type])
            ->andFilterWhere(['ilike', 'framework_name', $this->framework_name])
            ->andFilterWhere(['ilike', 'threat_actor', $this->threat_actor])
            ->andFilterWhere(['ilike', 'threat_attack_id', $this->threat_attack_id])
            ->andFilterWhere(['ilike', 'attack_type', $this->attack_type])
            ->andFilterWhere(['ilike', 'source_country', $this->source_country])
            ->andFilterWhere(['ilike', 'source_city', $this->source_city])
            ->andFilterWhere(['ilike', 'destination_country', $this->destination_country])
            ->andFilterWhere(['ilike', 'destination_city', $this->destination_city])
            ->andFilterWhere(['ilike', 'source_code', $this->source_code])
            ->andFilterWhere(['ilike', 'destination_code', $this->destination_code])
            ->andFilterWhere(['ilike', 'parent_events', $this->parent_events])
            ->andFilterWhere(['ilike', 'cef_extensions', $this->cef_extensions])
            ->andFilterWhere(['ilike', 'raw_event', $this->raw_event]);
        
        $query->all();


        return $dataProvider;
    }
}
