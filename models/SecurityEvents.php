<?php

namespace app\models;

use Yii;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use app\commands\SeverityCalculator;
use app\components\filter\FilterQuery;
use app\components\filter\FilterTypeEnum;
use app\models\BaseEvent;

/**
 * This is the model class for table "security_events".
 *
 * @property int $id
 * @property string|null $datetime
 * @property string $type
 * @property string $cef_version
 * @property int $cef_severity
 * @property string $cef_event_class_id
 * @property string $cef_device_product
 * @property string $cef_vendor
 * @property string $cef_device_version
 * @property string $cef_name
 * @property string|null $device_action
 * @property string|null $application_protocol
 * @property string|null $device_custom_ipv6_address1
 * @property string|null $device_custom_ipv6_address1_label
 * @property string|null $device_custom_ipv6_address2
 * @property string|null $device_custom_ipv6_address2_label
 * @property string|null $device_custom_ipv6_address3
 * @property string|null $device_custom_ipv6_address3_label
 * @property string|null $device_custom_ipv6_address4
 * @property string|null $device_custom_ipv6_address4_label
 * @property string|null $device_event_category
 * @property float|null $device_custom_floating_point1
 * @property string|null $device_custom_floating_point1_label
 * @property float|null $device_custom_floating_point2
 * @property string|null $device_custom_floating_point2_label
 * @property float|null $device_custom_floating_point3
 * @property string|null $device_custom_floating_point3_label
 * @property float|null $device_custom_floating_point4
 * @property string|null $device_custom_floating_point4_label
 * @property int|null $device_custom_number1
 * @property string|null $device_custom_number1_label
 * @property int|null $device_custom_number2
 * @property string|null $device_custom_number2_label
 * @property int|null $device_custom_number3
 * @property string|null $device_custom_number3_label
 * @property int|null $baseEventCount
 * @property string|null $device_custom_string1
 * @property string|null $device_custom_string1_label
 * @property string|null $device_custom_string2
 * @property string|null $device_custom_string2_label
 * @property string|null $device_custom_string3
 * @property string|null $device_custom_string3_label
 * @property string|null $device_custom_string4
 * @property string|null $device_custom_string4_label
 * @property string|null $device_custom_string5
 * @property string|null $device_custom_string5_label
 * @property string|null $device_custom_string6
 * @property string|null $device_custom_string6_label
 * @property string|null $device_custom_date1
 * @property string|null $device_custom_date1_label
 * @property string|null $device_custom_date2
 * @property string|null $device_custom_date2_label
 * @property int|null $device_direction
 * @property string|null $device_dns_domain
 * @property string|null $device_external_id
 * @property string|null $device_facility
 * @property string|null $device_inbound_interface
 * @property string|null $device_nt_domain
 * @property string|null $device_outbound_interface
 * @property string|null $device_payload_id
 * @property string|null $device_process_name
 * @property string|null $device_translated_address
 * @property string|null $device_time_zone
 * @property string|null $device_address
 * @property string|null $device_host_name
 * @property string|null $device_mac_address
 * @property int|null $device_process_id 
 * @property string|null $destination_host_name
 * @property string|null $destination_mac_address
 * @property string|null $destination_nt_domain
 * @property string|null $destination_dns_domain
 * @property string|null $destination_service_name
 * @property string|null $destination_translated_address
 * @property int|null $destination_translated_port
 * @property int|null $destination_process_id
 * @property string|null $destination_user_privileges
 * @property string|null $destination_process_name
 * @property int|null $destination_port
 * @property string|null $destination_address
 * @property string|null $destination_user_id
 * @property string|null $destination_user_name
 * @property string|null $destination_group_id
 * @property string|null $destination_group_name
 * @property string|null $end_time
 * @property string|null $external_id
 * @property string|null $file_create_time
 * @property string|null $file_hash
 * @property string|null $file_id
 * @property string|null $file_modification_time
 * @property string|null $file_name
 * @property string|null $file_path
 * @property string|null $file_permission
 * @property int|null $file_size
 * @property string|null $file_type
 * @property string|null $old_file_create_time
 * @property string|null $old_file_hash
 * @property string|null $old_file_id
 * @property string|null $old_file_modification_time
 * @property string|null $old_file_name
 * @property string|null $old_file_path
 * @property string|null $old_file_permission
 * @property int|null $old_file_size
 * @property string|null $old_file_type
 * @property string|null $flex_date1
 * @property string|null $flex_date1_label
 * @property string|null $flex_string1
 * @property string|null $flex_string1_label
 * @property string|null $flex_string2
 * @property string|null $flex_string2_label
 * @property int|null $bytes_in
 * @property int|null $bytes_out
 * @property string|null $message
 * @property string|null $event_outcome
 * @property string|null $transport_protocol
 * @property string|null $reason
 * @property string|null $request_url
 * @property string|null $request_client_application
 * @property string|null $request_context
 * @property string|null $request_cookies
 * @property string|null $request_method
 * @property string|null $device_receipt_time
 * @property string|null $source_host_name
 * @property string|null $source_mac_address
 * @property string|null $source_nt_domain
 * @property string|null $source_dns_domain
 * @property string|null $source_service_name
 * @property string|null $source_translated_address
 * @property int|null $source_translated_port
 * @property int|null $source_process_id
 * @property string|null $source_user_privileges
 * @property string|null $source_process_name
 * @property int|null $source_port
 * @property string|null $source_address
 * @property string|null $source_user_id
 * @property string|null $source_user_name
 * @property string|null $source_group_id
 * @property string|null $source_group_name
 * @property string|null $start_time
 * @property int|null $agent_translated_zone_key
 * @property int|null $agent_zone_key
 * @property int|null $customer_key
 * @property int|null $destination_translated_zone_key
 * @property int|null $destination_zone_key
 * @property int|null $device_translated_zone_key
 * @property int|null $device_zone_key
 * @property int|null $source_translated_zone_key
 * @property int|null $source_zone_key
 * @property int|null $reported_duration
 * @property string|null $reported_resource_group_name
 * @property string|null $reported_resource_id
 * @property string|null $reported_resource_name
 * @property string|null $reported_resource_type
 * @property string|null $framework_name
 * @property string|null $threat_actor
 * @property string|null $threat_attack_id
 * @property string|null $attack_type
 * @property string|null $source_country
 * @property string|null $source_city
 * @property string|null $destination_country
 * @property string|null $destination_city
 * @property float|null $source_geo_longitude
 * @property float|null $source_geo_latitude
 * @property float|null $destination_geo_longitude
 * @property float|null $destination_geo_latitude
 * @property int|null $destination_ip_network_model
 * @property int|null $source_ip_network_model
 * @property string|null $source_code
 * @property string|null $destination_code
 * @property string|null $parent_events
 * @property bool|null $analyzed
 * @property string|null $cef_extensions
 * @property string|null $raw_event
 *
 * @property AnalyzedEvents[] $analyzedEvents
 * @property AnalyzedSecurityEventsList[] $analyzedSecurityEventsLists
 * @property ClusteredEventsRelations[] $clusteredEventsRelations
 */
class SecurityEvents extends BaseEvent //\yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'security_events';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime', 'device_custom_date1', 'device_custom_date2', 'end_time', 'file_create_time', 'file_modification_time', 'old_file_create_time', 'old_file_modification_time', 'flex_date1', 'device_receipt_time', 'start_time'], 'safe'],
            [['cef_severity', 'device_custom_number1', 'device_custom_number2', 'device_custom_number3', 'baseEventCount', 'device_direction', 'device_process_id', 'destination_translated_port', 'destination_process_id', 'destination_port', 'file_size', 'old_file_size', 'bytes_in', 'bytes_out', 'source_translated_port', 'source_process_id', 'source_port', 'agent_translated_zone_key', 'agent_zone_key', 'customer_key', 'destination_translated_zone_key', 'destination_zone_key', 'device_translated_zone_key', 'device_zone_key', 'source_translated_zone_key', 'source_zone_key', 'reported_duration', 'destination_ip_network_model', 'source_ip_network_model'], 'default', 'value' => null],
            [['cef_severity', 'device_custom_number1', 'device_custom_number2', 'device_custom_number3', 'baseEventCount', 'device_direction', 'device_process_id', 'destination_translated_port', 'destination_process_id', 'destination_port', 'file_size', 'old_file_size', 'bytes_in', 'bytes_out', 'source_translated_port', 'source_process_id', 'source_port', 'agent_translated_zone_key', 'agent_zone_key', 'customer_key', 'destination_translated_zone_key', 'destination_zone_key', 'device_translated_zone_key', 'device_zone_key', 'source_translated_zone_key', 'source_zone_key', 'reported_duration', 'destination_ip_network_model', 'source_ip_network_model'], 'integer'],
            [['cef_version', 'cef_severity', 'cef_event_class_id', 'cef_device_product', 'cef_vendor', 'cef_device_version', 'cef_name'], 'required'],
            [['device_custom_floating_point1', 'device_custom_floating_point2', 'device_custom_floating_point3', 'device_custom_floating_point4', 'source_geo_longitude', 'source_geo_latitude', 'destination_geo_longitude', 'destination_geo_latitude'], 'number'],
            [['parent_events', 'cef_extensions', 'raw_event'], 'string'],
            [['analyzed'], 'boolean'],
            [['type', 'cef_version', 'application_protocol', 'transport_protocol'], 'string', 'max' => 31],
            [['cef_event_class_id', 'device_custom_ipv6_address1_label', 'device_custom_ipv6_address2_label', 'device_custom_ipv6_address3_label', 'device_custom_ipv6_address4_label', 'device_event_category', 'device_custom_floating_point1_label', 'device_custom_floating_point2_label', 'device_custom_floating_point3_label', 'device_custom_floating_point4_label', 'device_custom_number1_label', 'device_custom_number2_label', 'device_custom_number3_label', 'device_custom_string1_label', 'device_custom_string2_label', 'device_custom_string3_label', 'device_custom_string4_label', 'device_custom_string5_label', 'device_custom_string6_label', 'device_custom_date1_label', 'device_custom_date2_label', 'device_facility', 'device_process_name', 'destination_host_name', 'destination_service_name', 'destination_user_privileges', 'destination_process_name', 'destination_user_id', 'destination_user_name', 'destination_group_id', 'destination_group_name', 'file_id', 'file_name', 'file_path', 'file_permission', 'file_type', 'old_file_id', 'old_file_name', 'old_file_path', 'old_file_permission', 'old_file_type', 'flex_string1', 'flex_string2', 'message', 'reason', 'request_url', 'request_client_application', 'request_cookies', 'request_method', 'source_host_name', 'source_service_name', 'source_user_privileges', 'source_process_name', 'source_user_id', 'source_user_name', 'source_group_id', 'source_group_name'], 'string', 'max' => 1023],
            [['cef_device_product', 'cef_vendor', 'device_action', 'event_outcome'], 'string', 'max' => 63],
            [['cef_device_version', 'device_custom_ipv6_address1', 'device_custom_ipv6_address2', 'device_custom_ipv6_address3', 'device_custom_ipv6_address4', 'device_dns_domain', 'device_external_id', 'device_nt_domain', 'device_translated_address', 'device_time_zone', 'device_address', 'device_mac_address', 'destination_mac_address', 'destination_nt_domain', 'destination_dns_domain', 'destination_translated_address', 'destination_address', 'file_hash', 'old_file_hash', 'source_mac_address', 'source_nt_domain', 'source_dns_domain', 'source_translated_address', 'source_address', 'source_code', 'destination_code'], 'string', 'max' => 255],
            [['cef_name'], 'string', 'max' => 512],
            [['device_custom_string1', 'device_custom_string2', 'device_custom_string3', 'device_custom_string4', 'device_custom_string5', 'device_custom_string6'], 'string', 'max' => 4000],
            [['device_inbound_interface', 'device_outbound_interface', 'device_payload_id', 'flex_date1_label', 'flex_string1_label', 'flex_string2_label', 'reported_resource_group_name'], 'string', 'max' => 128],
            [['device_host_name'], 'string', 'max' => 100],
            [['external_id', 'threat_actor'], 'string', 'max' => 40],
            [['request_context'], 'string', 'max' => 2048],
            [['reported_resource_id', 'framework_name'], 'string', 'max' => 256],
            [['reported_resource_name', 'reported_resource_type', 'attack_type', 'source_country', 'source_city', 'destination_country', 'destination_city'], 'string', 'max' => 64],
            [['threat_attack_id'], 'string', 'max' => 32],
        ];
    }

    public static function columns()
      {
            return [
                    'id' => [ FilterTypeEnum::COMPARE ],
                    'datetime' => [ FilterTypeEnum::DATE ],
                    'type' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'cef_version' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'cef_severity' => [ FilterTypeEnum::COMPARE ],
                    'cef_event_class_id' => [ FilterTypeEnum::COMPARE ],
                    'cef_device_product' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'cef_vendor' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'cef_device_version' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'cef_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_action' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'application_protocol' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_ipv6_address1' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_ipv6_address1_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_ipv6_address2' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_ipv6_address2_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_ipv6_address3' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_ipv6_address3_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_ipv6_address4' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_ipv6_address4_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_event_category' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_floating_point1' => [ FilterTypeEnum::COMPARE ],
                    'device_custom_floating_point1_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_floating_point2' => [ FilterTypeEnum::COMPARE ],
                    'device_custom_floating_point2_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_floating_point3' => [ FilterTypeEnum::COMPARE ],
                    'device_custom_floating_point3_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_floating_point4' => [ FilterTypeEnum::COMPARE ],
                    'device_custom_floating_point4_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_number1' => [ FilterTypeEnum::COMPARE ],
                    'device_custom_number1_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_number2' => [ FilterTypeEnum::COMPARE ],
                    'device_custom_number2_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_number3' => [ FilterTypeEnum::COMPARE ],
                    'device_custom_number3_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'baseEventCount' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_string1' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_string1_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_string2' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_string2_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_string3' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_string3_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_string4' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_string4_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_string5' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_string5_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_string6' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_string6_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_date1' => [ FilterTypeEnum::DATE ],
                    'device_custom_date1_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_custom_date2' => [ FilterTypeEnum::DATE ],
                    'device_custom_date2_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_direction' => [ FilterTypeEnum::COMPARE ],
                    'device_dns_domain' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_external_id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_facility' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_inbound_interface' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_nt_domain' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_outbound_interface' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_payload_id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_process_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_translated_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_time_zone' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_host_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_mac_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_process_id' => [ FilterTypeEnum::COMPARE ],
                    'destination_host_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_mac_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_nt_domain' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_dns_domain' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_service_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_translated_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_translated_port' => [ FilterTypeEnum::COMPARE ],
                    'destination_process_id' => [ FilterTypeEnum::COMPARE ],
                    'destination_user_privileges' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_process_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_port' => [ FilterTypeEnum::COMPARE ],
                    'destination_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_user_id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_user_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_group_id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_group_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'end_time' => [ FilterTypeEnum::DATE ],
                    'external_id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'file_create_time' => [ FilterTypeEnum::DATE ],
                    'file_hash' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'file_id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'file_modification_time' => [ FilterTypeEnum::DATE ],
                    'file_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'file_path' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'file_permission' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'file_size' => [ FilterTypeEnum::COMPARE ],
                    'file_type' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'old_file_create_time' => [ FilterTypeEnum::DATE ],
                    'old_file_hash' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'old_file_id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'old_file_modification_time' => [ FilterTypeEnum::DATE ],
                    'old_file_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'old_file_path' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'old_file_permission' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'old_file_size' => [ FilterTypeEnum::COMPARE ],
                    'old_file_type' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'flex_date1' => [ FilterTypeEnum::DATE ],
                    'flex_date1_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'flex_string1' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'flex_string1_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'flex_string2' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'flex_string2_label' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'bytes_in' => [ FilterTypeEnum::COMPARE ],
                    'bytes_out' => [ FilterTypeEnum::COMPARE ],
                    'message' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'event_outcome' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'transport_protocol' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'reason' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'request_url' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'request_client_application' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'request_context' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'request_cookies' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'request_method' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'device_receipt_time' => [ FilterTypeEnum::DATE ],
                    'source_host_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_mac_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_nt_domain' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_dns_domain' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_service_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_translated_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_translated_port' => [ FilterTypeEnum::COMPARE ],
                    'source_process_id' => [ FilterTypeEnum::COMPARE ],
                    'source_user_privileges' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_process_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_port' => [ FilterTypeEnum::COMPARE ],
                    'source_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_user_id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_user_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_group_id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_group_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'start_time' => [ FilterTypeEnum::DATE ],
                    'agent_translated_zone_key' => [ FilterTypeEnum::COMPARE ],
                    'agent_zone_key' => [ FilterTypeEnum::COMPARE ],
                    'customer_key' => [ FilterTypeEnum::COMPARE ],
                    'destination_translated_zone_key' => [ FilterTypeEnum::COMPARE ],
                    'destination_zone_key' => [ FilterTypeEnum::COMPARE ],
                    'device_translated_zone_key' => [  FilterTypeEnum::COMPARE ],
                    'device_zone_key' => [ FilterTypeEnum::COMPARE ],
                    'source_translated_zone_key' => [ FilterTypeEnum::COMPARE ],
                    'source_zone_key' => [ FilterTypeEnum::COMPARE ],
                    'reported_duration' => [ FilterTypeEnum::COMPARE ],
                    'reported_resource_group_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'reported_resource_id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'reported_resource_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'reported_resource_type' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'framework_name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'threat_actor' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'threat_attack_id' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'attack_type' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_country' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_city' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_country' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_city' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'source_geo_longitude' => [ FilterTypeEnum::COMPARE ],
                    'source_geo_latitude' => [ FilterTypeEnum::COMPARE ],
                    'destination_geo_longitude' => [ FilterTypeEnum::COMPARE ],
                    'destination_geo_latitude' => [ FilterTypeEnum::COMPARE ],
                    'destination_ip_network_model' => [ FilterTypeEnum::COMPARE ],
                    'source_ip_network_model' => [ FilterTypeEnum::COMPARE ],
                    'source_code' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'destination_code' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'parent_events' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'analyzed' => [ FilterTypeEnum::COMPARE ],
                    'cef_extensions' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
                    'raw_event' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            ];
      }

    /**
     * {@inheritdoc}
     */
    public static function labels()
    {
        return [
            'id' => 'ID',
            'datetime' => 'Datetime',
            'type' => 'Event Type',
            'cef_version' => 'Cef Version',
            'cef_severity' => 'Cef Severity',
            'cef_event_class_id' => 'Cef Event Class ID',
            'cef_device_product' => 'Cef Device Product',
            'cef_vendor' => 'Cef Vendor',
            'cef_device_version' => 'Cef Device Version',
            'cef_name' => 'Cef Name',
            'device_action' => 'Device Action',
            'application_protocol' => 'Application Protocol',
            'device_custom_ipv6_address1' => 'Device Custom Ipv6 Address1',
            'device_custom_ipv6_address1_label' => 'Device Custom Ipv6 Address1 Label',
            'device_custom_ipv6_address2' => 'Device Custom Ipv6 Address2',
            'device_custom_ipv6_address2_label' => 'Device Custom Ipv6 Address2 Label',
            'device_custom_ipv6_address3' => 'Device Custom Ipv6 Address3',
            'device_custom_ipv6_address3_label' => 'Device Custom Ipv6 Address3 Label',
            'device_custom_ipv6_address4' => 'Device Custom Ipv6 Address4',
            'device_custom_ipv6_address4_label' => 'Device Custom Ipv6 Address4 Label',
            'device_event_category' => 'Device Event Category',
            'device_custom_floating_point1' => 'Device Custom Floating Point1',
            'device_custom_floating_point1_label' => 'Device Custom Floating Point1 Label',
            'device_custom_floating_point2' => 'Device Custom Floating Point2',
            'device_custom_floating_point2_label' => 'Device Custom Floating Point2 Label',
            'device_custom_floating_point3' => 'Device Custom Floating Point3',
            'device_custom_floating_point3_label' => 'Device Custom Floating Point3 Label',
            'device_custom_floating_point4' => 'Device Custom Floating Point4',
            'device_custom_floating_point4_label' => 'Device Custom Floating Point4 Label',
            'device_custom_number1' => 'Device Custom Number1',
            'device_custom_number1_label' => 'Device Custom Number1 Label',
            'device_custom_number2' => 'Device Custom Number2',
            'device_custom_number2_label' => 'Device Custom Number2 Label',
            'device_custom_number3' => 'Device Custom Number3',
            'device_custom_number3_label' => 'Device Custom Number3 Label',
            'baseEventCount' => 'Base Event Count',
            'device_custom_string1' => 'Device Custom String1',
            'device_custom_string1_label' => 'Device Custom String1 Label',
            'device_custom_string2' => 'Device Custom String2',
            'device_custom_string2_label' => 'Device Custom String2 Label',
            'device_custom_string3' => 'Device Custom String3',
            'device_custom_string3_label' => 'Device Custom String3 Label',
            'device_custom_string4' => 'Device Custom String4',
            'device_custom_string4_label' => 'Device Custom String4 Label',
            'device_custom_string5' => 'Device Custom String5',
            'device_custom_string5_label' => 'Device Custom String5 Label',
            'device_custom_string6' => 'Device Custom String6',
            'device_custom_string6_label' => 'Device Custom String6 Label',
            'device_custom_date1' => 'Device Custom Date1',
            'device_custom_date1_label' => 'Device Custom Date1 Label',
            'device_custom_date2' => 'Device Custom Date2',
            'device_custom_date2_label' => 'Device Custom Date2 Label',
            'device_direction' => 'Device Direction',
            'device_dns_domain' => 'Device Dns Domain',
            'device_external_id' => 'Device External ID',
            'device_facility' => 'Device Facility',
            'device_inbound_interface' => 'Device Inbound Interface',
            'device_nt_domain' => 'Device Nt Domain',
            'device_outbound_interface' => 'Device Outbound Interface',
            'device_payload_id' => 'Device Payload ID',
            'device_process_name' => 'Device Process Name',
            'device_translated_address' => 'Device Translated Address',
            'device_time_zone' => 'Device Time Zone',
            'device_address' => 'Device Address',
            'device_host_name' => 'Device Host Name',
            'device_mac_address' => 'Device Mac Address',
            'device_process_id' => 'Device Process ID',
            'destination_host_name' => 'Destination Host Name',
            'destination_mac_address' => 'Destination Mac Address',
            'destination_nt_domain' => 'Destination Nt Domain',
            'destination_dns_domain' => 'Destination Dns Domain',
            'destination_service_name' => 'Destination Service Name',
            'destination_translated_address' => 'Destination Translated Address',
            'destination_translated_port' => 'Destination Translated Port',
            'destination_process_id' => 'Destination Process ID',
            'destination_user_privileges' => 'Destination User Privileges',
            'destination_process_name' => 'Destination Process Name',
            'destination_port' => 'Destination Port',
            'destination_address' => 'Destination Address',
            'destination_user_id' => 'Destination User ID',
            'destination_user_name' => 'Destination User Name',
            'destination_group_id' => 'Destination Group ID',
            'destination_group_name' => 'Destination Group Name',
            'end_time' => 'End Time',
            'external_id' => 'External ID',
            'file_create_time' => 'File Create Time',
            'file_hash' => 'File Hash',
            'file_id' => 'File ID',
            'file_modification_time' => 'File Modification Time',
            'file_name' => 'File Name',
            'file_path' => 'File Path',
            'file_permission' => 'File Permission',
            'file_size' => 'File Size',
            'file_type' => 'File Type',
            'old_file_create_time' => 'Old File Create Time',
            'old_file_hash' => 'Old File Hash',
            'old_file_id' => 'Old File ID',
            'old_file_modification_time' => 'Old File Modification Time',
            'old_file_name' => 'Old File Name',
            'old_file_path' => 'Old File Path',
            'old_file_permission' => 'Old File Permission',
            'old_file_size' => 'Old File Size',
            'old_file_type' => 'Old File Type',
            'flex_date1' => 'Flex Date1',
            'flex_date1_label' => 'Flex Date1 Label',
            'flex_string1' => 'Flex String1',
            'flex_string1_label' => 'Flex String1 Label',
            'flex_string2' => 'Flex String2',
            'flex_string2_label' => 'Flex String2 Label',
            'bytes_in' => 'Bytes In',
            'bytes_out' => 'Bytes Out',
            'message' => 'Message',
            'event_outcome' => 'Event Outcome',
            'transport_protocol' => 'Transport Protocol',
            'reason' => 'Reason',
            'request_url' => 'Request Url',
            'request_client_application' => 'Request Client Application',
            'request_context' => 'Request Context',
            'request_cookies' => 'Request Cookies',
            'request_method' => 'Request Method',
            'device_receipt_time' => 'Device Receipt Time',
            'source_host_name' => 'Source Host Name',
            'source_mac_address' => 'Source Mac Address',
            'source_nt_domain' => 'Source Nt Domain',
            'source_dns_domain' => 'Source Dns Domain',
            'source_service_name' => 'Source Service Name',
            'source_translated_address' => 'Source Translated Address',
            'source_translated_port' => 'Source Translated Port',
            'source_process_id' => 'Source Process ID',
            'source_user_privileges' => 'Source User Privileges',
            'source_process_name' => 'Source Process Name',
            'source_port' => 'Source Port',
            'source_address' => 'Source Address',
            'source_user_id' => 'Source User ID',
            'source_user_name' => 'Source User Name',
            'source_group_id' => 'Source Group ID',
            'source_group_name' => 'Source Group Name',
            'start_time' => 'Start Time',
            'agent_translated_zone_key' => 'Agent Translated Zone Key',
            'agent_zone_key' => 'Agent Zone Key',
            'customer_key' => 'Customer Key',
            'destination_translated_zone_key' => 'Destination Translated Zone Key',
            'destination_zone_key' => 'Destination Zone Key',
            'device_translated_zone_key' => 'Device Translated Zone Key',
            'device_zone_key' => 'Device Zone Key',
            'source_translated_zone_key' => 'Source Translated Zone Key',
            'source_zone_key' => 'Source Zone Key',
            'reported_duration' => 'Reported Duration',
            'reported_resource_group_name' => 'Reported Resource Group Name',
            'reported_resource_id' => 'Reported Resource ID',
            'reported_resource_name' => 'Reported Resource Name',
            'reported_resource_type' => 'Reported Resource Type',
            'framework_name' => 'Framework Name',
            'threat_actor' => 'Threat Actor',
            'threat_attack_id' => 'Threat Attack ID',
            'attack_type' => 'Attack Type',
            'source_country' => 'Source Country',
            'source_city' => 'Source City',
            'destination_country' => 'Destination Country',
            'destination_city' => 'Destination City',
            'source_geo_longitude' => 'Source Geo Longitude',
            'source_geo_latitude' => 'Source Geo Latitude',
            'destination_geo_longitude' => 'Destination Geo Longitude',
            'destination_geo_latitude' => 'Destination Geo Latitude',
            'destination_ip_network_model' => 'Destination Ip Network Model',
            'source_ip_network_model' => 'Source Ip Network Model',
            'source_code' => 'Source Code',
            'destination_code' => 'Destination Code',
            'parent_events' => 'Parent Events',
            'analyzed' => 'Analyzed',
            'cef_extensions' => 'Cef Extensions',
            'raw_event' => 'Raw Event',
        ];
    }

    /**
     * Gets query for [[AnalyzedEvents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnalyzedEvents()
    {
        return $this->hasMany(AnalyzedEvents::className(), ['security_events_id' => 'id']);
    }

    /**
     * Gets query for [[AnalyzedSecurityEventsLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnalyzedSecurityEventsLists()
    {
        return $this->hasMany(AnalyzedSecurityEventsList::className(), ['analzyed_security_events_id' => 'id']);
    }

    /**
     * Gets query for [[ClusteredEventsRelations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClusteredEventsRelations()
    {
        return $this->hasMany(ClusteredEventsRelations::className(), ['fk_event_id' => 'id']);
    }

    public static function getCefHeader($cefString)
	{
        $event = new self();

		$data = explode('|', $cefString);

		$dateHost = explode(' ', strrev(array_shift($data)), 3);
		$event->cef_version = str_replace('CEF:', '', strrev(array_shift($dateHost)));
        $event->device_host_name= strrev(array_shift($dateHost));
        
        // osetrenie ak na zaciatku logu nie je timestamp
        $strDate = array_shift($dateHost);
        if(!empty($strDate))
        {
		    $event->datetime = date('Y-m-d H:i:s', strtotime(strrev($strDate)));
        }
        else
        {
            date_default_timezone_set('Europe/Bratislava');
            $event->datetime = date('Y-m-d H:i:s');
        }

		$event->cef_vendor = array_shift($data);
		$event->cef_device_product = array_shift($data);
		$event->cef_device_version = array_shift($data);
		$event->cef_event_class_id = array_shift($data);
		$event->cef_name = array_shift($data);
		$event->cef_severity = array_shift($data);
        return $event;
    }

    public static function extractCefFields($cefString, $eventType)
	{
		$event = new self();
        $event = $event->getCefHeader($cefString);
        $event->type = $eventType;

		$data = explode('|', $cefString);

		for($i = 0; $i < 7; $i++)
		{
			array_shift($data);
		}

		$data = array_shift($data);
	
		preg_match('/rawEvent=(.*)/', $data, $matches);

		$raw = "";
		if(empty($matches))
		{
			$raw = null;
		}
		else
		{
			$raw = $matches[1];
		}

		$exData = explode(" ", $data);
		$values = [];
		foreach($exData as $val) {
			$tmp = explode("=", $val);
			if($tmp[0] == "rawEvent" || $tmp[0] == "reason")
			{
				break;
			}
            		$values[$tmp[0]] = isset($tmp[1]) ? $tmp[1] : "";
		}
		
		//get HTTP Request from event
		if(preg_match('/request=/', $data)){
			$start_pos = strpos($data, 'request=');
			$start_pos += strlen('request=');
			$end_pos = strpos($data, ' ', $start_pos);
			$length = $end_pos - $start_pos;
			$request_url = substr($data, $start_pos, $length);
		}
        //get Request Client Application
		if(preg_match('/requestClientApplication=/', $data)){
			$start_pos = strpos($data, 'requestClientApplication=');
			$start_pos += strlen('requestClientApplication=');
			$end_pos = strpos($data, ' rawEvent=', $start_pos);
			$length = $end_pos - $start_pos;
			$request_client_application = substr($data, $start_pos, $length);
		}

        $event->device_action = $values['act'] ?? "";
        $event->application_protocol = $values['app'] ?? "";
        $event->device_custom_ipv6_address1 = $values['c6a1'] ?? "";
        $event->device_custom_ipv6_address1_label = $values['c6a1Label'] ?? "";
        $event->device_custom_ipv6_address2 = $values['c6a2'] ?? "";
        $event->device_custom_ipv6_address2_label = $values['c6a2Label'] ?? "";
        $event->device_custom_ipv6_address3 = $values['c6a3'] ?? "";
        $event->device_custom_ipv6_address3_label = $values['c6a3Label'] ?? "";
        $event->device_custom_ipv6_address4 = $values['c6a4'] ?? "";
        $event->device_custom_ipv6_address4_label = $values['c6a4Label'] ?? "";
        $event->device_event_category = $values['cat'] ?? "";
        $event->device_custom_floating_point1 = $values['cfp1'] ?? "";
        $event->device_custom_floating_point1_label = $values['cfp1Label'] ?? "";
        $event->device_custom_floating_point2 = $values['cfp2'] ?? "";
        $event->device_custom_floating_point2_label = $values['cfp2Label'] ?? "";
        $event->device_custom_floating_point3 = $values['cfp3'] ?? "";
        $event->device_custom_floating_point3_label = $values['cfp3Label'] ?? "";
        $event->device_custom_floating_point4 = $values['cfp4'] ?? "";
        $event->device_custom_floating_point4_label = $values['cfp4Label'] ?? "";
        $event->device_custom_number1 = $values['cn1'] ?? "";
        $event->device_custom_number1_label = $values['cn1Label'] ?? "";
        $event->device_custom_number2 = $values['cn2'] ?? "";
        $event->device_custom_number2_label = $values['cn2Label'] ?? "";
        $event->device_custom_number3 = $values['cn3'] ?? "";
        $event->device_custom_number3_label = $values['cn3Label'] ?? "";
        $event->baseEventCount = $values['cnt'] ?? "";
        $event->device_custom_string1 = $values['cs1'] ?? "";
        $event->device_custom_string1_label = $values['cs1Label'] ?? "";
        $event->device_custom_string2 = $values['cs2'] ?? "";
        $event->device_custom_string2_label = $values['cs2Label'] ?? "";
        $event->device_custom_string3 = $values['cs3'] ?? "";
        $event->device_custom_string3_label = $values['cs3Label'] ?? "";
        $event->device_custom_string4 = $values['cs4'] ?? "";
        $event->device_custom_string4_label = $values['cs4Label'] ?? "";
        $event->device_custom_string5 = $values['cs5'] ?? "";
        $event->device_custom_string5_label = $values['cs5Label'] ?? "";
        $event->device_custom_string6 = $values['cs6'] ?? "";
        $event->device_custom_string6_label = $values['cs6Label'] ?? "";
        $event->device_custom_date1 = $values['deviceCustomDate1'] ?? "";
        $event->device_custom_date1_label = $values['deviceCustomDate1Label'] ?? "";
        $event->device_custom_date2 = $values['deviceCustomDate2'] ?? "";
        $event->device_custom_date2_label = $values['deviceCustomDate2Label'] ?? "";
        $event->device_direction = $values['deviceDirection'] ?? "";
        $event->device_dns_domain = $values['deviceDns Domain'] ?? "";
        $event->device_external_id = $values['device ExternalId'] ?? "";
        $event->device_facility = $values['deviceFacility'] ?? "";
        $event->device_inbound_interface = $values['deviceInboundInterface'] ?? "";
        $event->device_nt_domain = $values['deviceNt Domain'] ?? "";
        $event->device_outbound_interface = $values['deviceOutboundInterface'] ?? "";
        $event->device_payload_id = $values['devicePayloadId'] ?? "";
        $event->device_process_name = $values['deviceProcess Name'] ?? "";
        $event->device_translated_address = $values['deviceTranslatedAddress'] ?? "";
        $event->device_time_zone = $values['dtz'] ?? "";
        $event->device_address = $values['dvc'] ?? "";
        if(!$event->device_host_name)
            $event->device_host_name = $values['dvchost'] ?? "";
        $event->device_mac_address = $values['dvcmac'] ?? "";
        $event->device_process_id = $values['dvcpid'] ?? "";
        $event->destination_host_name = $values['dhost'] ?? "";
        $event->destination_mac_address = $values['dmac'] ?? "";
        $event->destination_nt_domain = $values['dntdom'] ?? "";
        $event->destination_dns_domain = $values[''] ?? "";
        $event->destination_service_name = $values[''] ?? "";
        $event->destination_translated_address = $values[''] ?? "";
        $event->destination_translated_port = $values[''] ?? "";
        $event->destination_process_id = $values['dpid'] ?? "";
        $event->destination_user_privileges = $values['dpriv'] ?? "";
        $event->destination_process_name = $values['dproc'] ?? "";
        $event->destination_port = $values['dpt'] ?? "";
        $event->destination_address = $values['dst'] ?? "";
        $event->destination_user_id = $values['duid'] ?? "";
        $event->destination_user_name = $values['duser'] ?? "";
        $event->destination_group_id = $values['dgid'] ?? ""; //cs1
        $event->destination_group_name = $values['dgroup'] ?? "";//cn1
        $event->end_time = $values['end'] ?? "";
        $event->external_id = $values['externalId'] ?? "";
        $event->file_create_time = $values['fileCreateTime'] ?? "";
        $event->file_hash = $values['fileHash'] ?? "";
        $event->file_id = $values['fileId'] ?? "";
        $event->file_modification_time = $values['fileModificationTime'] ?? "";
        $event->file_name = $values['fname'] ?? "";
        $event->file_path = $values['filePath'] ?? "";
        $event->file_permission = $values['filePermission'] ?? "";
        $event->file_size = $values['fsize'] ?? "";
        $event->file_type = $values['fileType'] ?? "";
        $event->old_file_create_time = $values['oldFileCreateTime'] ?? "";
        $event->old_file_hash = $values['oldFileHash'] ?? "";
        $event->old_file_id = $values['oldFileId'] ?? "";
        $event->old_file_modification_time = $values['oldFileModificationTime'] ?? "";
        $event->old_file_name = $values['oldFileName'] ?? "";
        $event->old_file_path = $values['oldFilePath'] ?? "";
        $event->old_file_permission = $values['oldFile Permission'] ?? "";
        $event->old_file_size = $values['oldFileSize'] ?? "";
        $event->old_file_type = $values['oldFileType'] ?? "";
        $event->flex_date1 = $values['flexDate1'] ?? "";
        $event->flex_date1_label = $values['flexDate1Label'] ?? "";
        $event->flex_string1 = $values['flexString1'] ?? "";
        $event->flex_string1_label = $values['flexString1Label'] ?? "";
        $event->flex_string2 = $values['flexString2'] ?? "";
        $event->flex_string2_label = $values['flexString2Label'] ?? "";
        $event->bytes_in = $values['in'] ?? "";
        $event->bytes_out = $values['out'] ?? "";
        $event->message = $values['msg'] ?? "";
        $event->event_outcome = $values['outcome'] ?? "";
        $event->transport_protocol = $values['proto'] ?? $values['app'] ?? "";
        $event->reason = $values['reason'] ?? "";
        $event->request_method = $values['requestMethod'] ?? "";
		$event->request_url = $request_url ?? "";
		$event->request_client_application = $request_client_application ?? "";
        $event->request_context = $values['requestContext'] ?? "";
        $event->request_cookies = $values['requestCookies'] ?? "";
        $event->device_receipt_time = $values['rt'] ?? "";
        $event->source_host_name = $values['shost'] ?? "";
        $event->source_mac_address = $values['smac'] ?? "";
        $event->source_nt_domain = $values['sntdom'] ?? "";
        $event->source_dns_domain = $values['sourceDnsDomain'] ?? "";
        $event->source_service_name = $values['sourceServiceName'] ?? "";
        $event->source_translated_address = $values['sourceTranslatedAddress'] ?? "";
        $event->source_translated_port = $values['sourceTranslatedPort'] ?? "";
        $event->source_process_id = $values['spid'] ?? "";
        $event->source_user_privileges = $values['spriv'] ?? "";
        $event->source_process_name = $values['sproc'] ?? "";
        $event->source_port = $values['spt'] ?? "";
        $event->source_address = $values['src'] ?? "";
        if($event->source_address == "localhost"){
        $event->source_address = "127.0.0.1";
        }
        $event->source_user_id = $values['suid'] ?? "";
        $event->source_user_name = $values['suser'] ?? "";
        $event->source_group_id = $values['sgid'] ?? "";
        $event->source_group_name = $values['sgroup'] ?? "";
        $event->start_time = $values['start'] ?? "";
        $event->agent_translated_zone_key = $values['agentTranslatedZoneKey'] ?? "";
        $event->agent_zone_key = $values['agentZoneKey'] ?? "";
        $event->customer_key = $values['customerKey'] ?? "";
        $event->destination_translated_zone_key = $values['dTranslatedZoneKey'] ?? "";
        $event->destination_zone_key = $values['dZoneKey'] ?? "";
        $event->device_translated_zone_key = $values['deviceTranslatedZoneKey'] ?? "";
        $event->device_zone_key = $values['deviceZoneKey'] ?? "";
        $event->source_translated_zone_key = $values['sTranslatedZoneKey'] ?? "";
        $event->source_zone_key = $values['sZoneKey'] ?? "";
        $event->reported_duration = $values['reportedDuration'] ?? "";
        $event->reported_resource_group_name = $values['reportedResourceGroupName'] ?? "";
        $event->reported_resource_id = $values['reportedResourceID'] ?? "";
        $event->reported_resource_name = $values['reportedResourceName'] ?? "";
        $event->reported_resource_type = $values['reportedResourceType'] ?? "";
        $event->framework_name = $values['frameworkName'] ?? "";
        $event->threat_actor = $values['threatActor'] ?? "";
        $event->threat_attack_id = $values['threatAttackID'] ?? "";
        $event->attack_type = $values['att'] ?? "";
        $event->parent_events = $values['cs1'] ?? "";
        //$event->cef_extensions = $values[''] ?? "";

        if($event->type == 'normalized'){
            $event->cef_severity = SeverityCalculator::calculateSeverity($event);
            $event->raw_event = $raw;
        }
        else
            $event->raw_event = $cefString;

		//map netwrok model for src IP
		$position = strpos($cefString, "src_network_model_id=");
		if($position != FALSE){
            $event->source_ip_network_model = $cefString[$position + strlen("src_network_model_id=")];
		}

		//map network model for dst IP
		$position = strpos($cefString, "dst_network_model_id=");
		if($position != FALSE){
            $event->destination_ip_network_model = $cefString[$position + strlen("dst_network_model_id=")];
		}
		
		//map geoIP for src IP
		$position = strpos($cefString, "src_country_isoCode=");
		if($position != FALSE){
            $start_position = $position + strlen("src_country_isoCode=");
            $end_position = strpos($cefString, " ", $start_position);
            $event->source_code = substr($cefString, $start_position, $end_position - $start_position);
		}
		

		$position = strpos($cefString, "src_country_name=");
		if($position != FALSE){
            $start_position = $position + strlen("src_country_name=");
            $end_position = strpos($cefString, "src_", $start_position) - 1;
            $event->source_country = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "src_city_name=");
		if($position != FALSE){
            $start_position = $position + strlen("src_city_name=");
            $end_position = strpos($cefString, "src_", $start_position) - 1;
            $event->source_city = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "src_location_latitude=");
		if($position != FALSE){
            $start_position = $position + strlen("src_location_latitude=");
            $end_position = strpos($cefString, " ", $start_position);
            $event->source_geo_latitude = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "src_location_longitude=");
		if($position != FALSE){
            $start_position = $position + strlen("src_location_longitude=");
            $end_position = strpos($cefString, " ", $start_position);
            $event->source_geo_longitude = substr($cefString, $start_position, $end_position - $start_position);
		}
		
		//map geoIP for dst IP
		$position = strpos($cefString, "dst_country_isoCode=");
		if($position != FALSE){
            $start_position = $position + strlen("dst_country_isoCode=");
            $end_position = strpos($cefString, " ", $start_position);
            $event->destination_code = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "dst_country_name=");
		if($position != FALSE){
            $start_position = $position + strlen("dst_country_name=");
            $end_position = strpos($cefString, "dst_", $start_position) - 1;
            $event->destination_country = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "dst_city_name=");
		if($position != FALSE){
            $start_position = $position + strlen("dst_city_name=");
            $end_position = strpos($cefString, "dst_", $start_position) - 1;
            $event->destination_city = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "dst_location_latitude=");
		if($position != FALSE){
            $start_position = $position + strlen("dst_location_latitude=");
            $end_position = strpos($cefString, " ", $start_position);
            $event->destination_geo_latitude = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "dst_location_longitude=");
		if($position != FALSE){
            $start_position = $position + strlen("dst_location_longitude=");
            $end_position = strpos($cefString, " ", $start_position);
            $event->destination_geo_longitude = substr($cefString, $start_position, $end_position - $start_position);
		}

		return $event;
	}
}
