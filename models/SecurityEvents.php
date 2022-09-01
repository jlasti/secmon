<?php

namespace app\models;

use Yii;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use app\commands\SeverityCalculator;
use app\components\filter\FilterQuery;

/**
 * This is the model class for table "security_events".
 *
 * @property int $id
 * @property string|null $datetime
 * @property int|null $type
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
 * @property float|null $device_customfloating_point1
 * @property string|null $device_customfloating_point1_label
 * @property float|null $device_customfloating_point2
 * @property string|null $device_customfloating_point2_label
 * @property float|null $device_customfloating_point3
 * @property string|null $device_customfloating_point3_label
 * @property float|null $device_customfloating_point4
 * @property string|null $device_customfloating_point4_label
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
class SecurityEvents extends \yii\db\ActiveRecord
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
            [['type', 'cef_severity', 'device_custom_number1', 'device_custom_number2', 'device_custom_number3', 'baseEventCount', 'device_direction', 'device_process_id', 'destination_translated_port', 'destination_process_id', 'destination_port', 'file_size', 'old_file_size', 'bytes_in', 'bytes_out', 'source_translated_port', 'source_process_id', 'source_port', 'agent_translated_zone_key', 'agent_zone_key', 'customer_key', 'destination_translated_zone_key', 'destination_zone_key', 'device_translated_zone_key', 'device_zone_key', 'source_translated_zone_key', 'source_zone_key', 'reported_duration', 'destination_ip_network_model', 'source_ip_network_model'], 'default', 'value' => null],
            [['type', 'cef_severity', 'device_custom_number1', 'device_custom_number2', 'device_custom_number3', 'baseEventCount', 'device_direction', 'device_process_id', 'destination_translated_port', 'destination_process_id', 'destination_port', 'file_size', 'old_file_size', 'bytes_in', 'bytes_out', 'source_translated_port', 'source_process_id', 'source_port', 'agent_translated_zone_key', 'agent_zone_key', 'customer_key', 'destination_translated_zone_key', 'destination_zone_key', 'device_translated_zone_key', 'device_zone_key', 'source_translated_zone_key', 'source_zone_key', 'reported_duration', 'destination_ip_network_model', 'source_ip_network_model'], 'integer'],
            [['cef_version', 'cef_severity', 'cef_event_class_id', 'cef_device_product', 'cef_vendor', 'cef_device_version', 'cef_name'], 'required'],
            [['device_customfloating_point1', 'device_customfloating_point2', 'device_customfloating_point3', 'device_customfloating_point4', 'source_geo_longitude', 'source_geo_latitude', 'destination_geo_longitude', 'destination_geo_latitude'], 'number'],
            [['parent_events', 'cef_extensions', 'raw_event'], 'string'],
            [['analyzed'], 'boolean'],
            [['cef_version', 'application_protocol', 'transport_protocol'], 'string', 'max' => 31],
            [['cef_event_class_id', 'device_custom_ipv6_address1_label', 'device_custom_ipv6_address2_label', 'device_custom_ipv6_address3_label', 'device_custom_ipv6_address4_label', 'device_event_category', 'device_customfloating_point1_label', 'device_customfloating_point2_label', 'device_customfloating_point3_label', 'device_customfloating_point4_label', 'device_custom_number1_label', 'device_custom_number2_label', 'device_custom_number3_label', 'device_custom_string1_label', 'device_custom_string2_label', 'device_custom_string3_label', 'device_custom_string4_label', 'device_custom_string5_label', 'device_custom_string6_label', 'device_custom_date1_label', 'device_custom_date2_label', 'device_facility', 'device_process_name', 'destination_host_name', 'destination_service_name', 'destination_user_privileges', 'destination_process_name', 'destination_user_id', 'destination_user_name', 'destination_group_id', 'destination_group_name', 'file_id', 'file_name', 'file_path', 'file_permission', 'file_type', 'old_file_id', 'old_file_name', 'old_file_path', 'old_file_permission', 'old_file_type', 'flex_string1', 'flex_string2', 'message', 'reason', 'request_url', 'request_client_application', 'request_cookies', 'request_method', 'source_host_name', 'source_service_name', 'source_user_privileges', 'source_process_name', 'source_user_id', 'source_user_name', 'source_group_id', 'source_group_name'], 'string', 'max' => 1023],
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
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
            'device_customfloating_point1' => 'Device Customfloating Point1',
            'device_customfloating_point1_label' => 'Device Customfloating Point1 Label',
            'device_customfloating_point2' => 'Device Customfloating Point2',
            'device_customfloating_point2_label' => 'Device Customfloating Point2 Label',
            'device_customfloating_point3' => 'Device Customfloating Point3',
            'device_customfloating_point3_label' => 'Device Customfloating Point3 Label',
            'device_customfloating_point4' => 'Device Customfloating Point4',
            'device_customfloating_point4_label' => 'Device Customfloating Point4 Label',
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
    
    public static function extractCefHeader($cefString)
	{
		$event = new self();

		$data = explode('|', $cefString);

		$dateHost = explode(' ', strrev(array_shift($data)), 3);
		$event->cef_version = str_replace('CEF:', '', strrev(array_shift($dateHost)));
        $event->device_host_name= strrev(array_shift($dateHost));
        $event->type = 2;
        
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

        $data = array_shift($data);
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

        $event->parent_events = $values['cs1'] ?? "";
        $event->attack_type = $values['att'] ?? "";
		$event->raw_event = $cefString;

		return $event;
	}

    public static function extractCefFields($cefString)
	{
		$event = new self();
        $event = $event->extractCefHeader($cefString);
        $event->type = 0;

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
		
		//nevyhnutne dva if-y, lebo delimiter " " a "=" kazia vstup do atributov request_url a request_client_application
		if(preg_match('/request=/', $data)){
			$start_pos = strpos($data, 'request=');
			$start_pos += strlen('request=');
			$end_pos = strpos($data, ' ', $start_pos);
			$length = $end_pos - $start_pos;
			$request_url = substr($data, $start_pos, $length);
		}
		if(preg_match('/requestClientApplication=/', $data)){
			$start_pos = strpos($data, 'requestClientApplication=');
			$start_pos += strlen('requestClientApplication=');
			$end_pos = strpos($data, ' rawEvent=', $start_pos);
			$length = $end_pos - $start_pos;
			$request_client_application = substr($data, $start_pos, $length);
		}	

		$event->source_address = $values['src'] ?? "";
		if($event->source_address == "localhost"){
			$event->source_address = "127.0.0.1";
		}
		$event->destination_address = $values['dst'] ?? "";
		$event->source_mac_address = $values['smac'] ?? "";
		$event->destination_mac_address = $values['dmac'] ?? "";
		$event->source_port = $values['spt'] ?? "";
		$event->destination_port = $values['dpt'] ?? "";
		$event->application_protocol = $values['proto'] ?? $values['app'] ?? "";
		$event->request_method = $values['request_method'] ?? "";
		$event->request_url = $request_url ?? "";
		$event->request_client_application = $request_client_application ?? "";
		$event->destination_user_name = $values['duser'] ?? "";
		$event->destination_user_id = $values['duid'] ?? "";
		$event->destination_group_name = $values['cs1'] ?? "";
		$event->destination_group_id = $values['cn1'] ?? "";
		$event->device_process_id = $values['dvcpid'] ?? "";
		$event->source_user_privileges = $values['spriv'] ?? "";
		$event->destination_user_name = $values['cs2'] ?? "";
		$event->raw_event = $raw;

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
            $event->source_latitude = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "src_location_longitude=");
		if($position != FALSE){
            $start_position = $position + strlen("src_location_longitude=");
            $end_position = strpos($cefString, " ", $start_position);
            $event->source_longitude = substr($cefString, $start_position, $end_position - $start_position);
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
            $event->destination_latitude = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "dst_location_longitude=");
		if($position != FALSE){
            $start_position = $position + strlen("dst_location_longitude=");
            $end_position = strpos($cefString, " ", $start_position);
            $event->destination_longitude = substr($cefString, $start_position, $end_position - $start_position);
		}

		$event->cef_severity = SeverityCalculator::calculateSeverity($event);

		return $event;
	}
}
