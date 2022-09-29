<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SecurityEventsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="security-events-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'datetime') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'cef_version') ?>

    <?= $form->field($model, 'cef_severity') ?>

    <?php // echo $form->field($model, 'cef_event_class_id') ?>

    <?php // echo $form->field($model, 'cef_device_product') ?>

    <?php // echo $form->field($model, 'cef_vendor') ?>

    <?php // echo $form->field($model, 'cef_device_version') ?>

    <?php // echo $form->field($model, 'cef_name') ?>

    <?php // echo $form->field($model, 'device_action') ?>

    <?php // echo $form->field($model, 'application_protocol') ?>

    <?php // echo $form->field($model, 'device_custom_ipv6_address1') ?>

    <?php // echo $form->field($model, 'device_custom_ipv6_address1_label') ?>

    <?php // echo $form->field($model, 'device_custom_ipv6_address2') ?>

    <?php // echo $form->field($model, 'device_custom_ipv6_address2_label') ?>

    <?php // echo $form->field($model, 'device_custom_ipv6_address3') ?>

    <?php // echo $form->field($model, 'device_custom_ipv6_address3_label') ?>

    <?php // echo $form->field($model, 'device_custom_ipv6_address4') ?>

    <?php // echo $form->field($model, 'device_custom_ipv6_address4_label') ?>

    <?php // echo $form->field($model, 'device_event_category') ?>

    <?php // echo $form->field($model, 'device_customfloating_point1') ?>

    <?php // echo $form->field($model, 'device_customfloating_point1_label') ?>

    <?php // echo $form->field($model, 'device_customfloating_point2') ?>

    <?php // echo $form->field($model, 'device_customfloating_point2_label') ?>

    <?php // echo $form->field($model, 'device_customfloating_point3') ?>

    <?php // echo $form->field($model, 'device_customfloating_point3_label') ?>

    <?php // echo $form->field($model, 'device_customfloating_point4') ?>

    <?php // echo $form->field($model, 'device_customfloating_point4_label') ?>

    <?php // echo $form->field($model, 'device_custom_number1') ?>

    <?php // echo $form->field($model, 'device_custom_number1_label') ?>

    <?php // echo $form->field($model, 'device_custom_number2') ?>

    <?php // echo $form->field($model, 'device_custom_number2_label') ?>

    <?php // echo $form->field($model, 'device_custom_number3') ?>

    <?php // echo $form->field($model, 'device_custom_number3_label') ?>

    <?php // echo $form->field($model, 'baseEventCount') ?>

    <?php // echo $form->field($model, 'device_custom_string1') ?>

    <?php // echo $form->field($model, 'device_custom_string1_label') ?>

    <?php // echo $form->field($model, 'device_custom_string2') ?>

    <?php // echo $form->field($model, 'device_custom_string2_label') ?>

    <?php // echo $form->field($model, 'device_custom_string3') ?>

    <?php // echo $form->field($model, 'device_custom_string3_label') ?>

    <?php // echo $form->field($model, 'device_custom_string4') ?>

    <?php // echo $form->field($model, 'device_custom_string4_label') ?>

    <?php // echo $form->field($model, 'device_custom_string5') ?>

    <?php // echo $form->field($model, 'device_custom_string5_label') ?>

    <?php // echo $form->field($model, 'device_custom_string6') ?>

    <?php // echo $form->field($model, 'device_custom_string6_label') ?>

    <?php // echo $form->field($model, 'device_custom_date1') ?>

    <?php // echo $form->field($model, 'device_custom_date1_label') ?>

    <?php // echo $form->field($model, 'device_custom_date2') ?>

    <?php // echo $form->field($model, 'device_custom_date2_label') ?>

    <?php // echo $form->field($model, 'device_direction') ?>

    <?php // echo $form->field($model, 'device_dns_domain') ?>

    <?php // echo $form->field($model, 'device_external_id') ?>

    <?php // echo $form->field($model, 'device_facility') ?>

    <?php // echo $form->field($model, 'device_inbound_interface') ?>

    <?php // echo $form->field($model, 'device_nt_domain') ?>

    <?php // echo $form->field($model, 'device_outbound_interface') ?>

    <?php // echo $form->field($model, 'device_payload_id') ?>

    <?php // echo $form->field($model, 'device_process_name') ?>

    <?php // echo $form->field($model, 'device_translated_address') ?>

    <?php // echo $form->field($model, 'device_time_zone') ?>

    <?php // echo $form->field($model, 'device_address') ?>

    <?php // echo $form->field($model, 'device_host_name') ?>

    <?php // echo $form->field($model, 'device_mac_address') ?>

    <?php // echo $form->field($model, 'device_process_id ') ?>

    <?php // echo $form->field($model, 'destination_host_name') ?>

    <?php // echo $form->field($model, 'destination_mac_address') ?>

    <?php // echo $form->field($model, 'destination_nt_domain') ?>

    <?php // echo $form->field($model, 'destination_dns_domain') ?>

    <?php // echo $form->field($model, 'destination_service_name') ?>

    <?php // echo $form->field($model, 'destination_translated_address') ?>

    <?php // echo $form->field($model, 'destination_translated_port') ?>

    <?php // echo $form->field($model, 'destination_process_id') ?>

    <?php // echo $form->field($model, 'destination_user_privileges') ?>

    <?php // echo $form->field($model, 'destination_process_name') ?>

    <?php // echo $form->field($model, 'destination_port') ?>

    <?php // echo $form->field($model, 'destination_address') ?>

    <?php // echo $form->field($model, 'destination_user_id') ?>

    <?php // echo $form->field($model, 'destination_user_name') ?>

    <?php // echo $form->field($model, 'destination_group_id') ?>

    <?php // echo $form->field($model, 'destination_group_name') ?>

    <?php // echo $form->field($model, 'end_time') ?>

    <?php // echo $form->field($model, 'external_id') ?>

    <?php // echo $form->field($model, 'file_create_time') ?>

    <?php // echo $form->field($model, 'file_hash') ?>

    <?php // echo $form->field($model, 'file_id') ?>

    <?php // echo $form->field($model, 'file_modification_time') ?>

    <?php // echo $form->field($model, 'file_name') ?>

    <?php // echo $form->field($model, 'file_path') ?>

    <?php // echo $form->field($model, 'file_permission') ?>

    <?php // echo $form->field($model, 'file_size') ?>

    <?php // echo $form->field($model, 'file_type') ?>

    <?php // echo $form->field($model, 'old_file_create_time') ?>

    <?php // echo $form->field($model, 'old_file_hash') ?>

    <?php // echo $form->field($model, 'old_file_id') ?>

    <?php // echo $form->field($model, 'old_file_modification_time') ?>

    <?php // echo $form->field($model, 'old_file_name') ?>

    <?php // echo $form->field($model, 'old_file_path') ?>

    <?php // echo $form->field($model, 'old_file_permission') ?>

    <?php // echo $form->field($model, 'old_file_size') ?>

    <?php // echo $form->field($model, 'old_file_type') ?>

    <?php // echo $form->field($model, 'flex_date1') ?>

    <?php // echo $form->field($model, 'flex_date1_label') ?>

    <?php // echo $form->field($model, 'flex_string1') ?>

    <?php // echo $form->field($model, 'flex_string1_label') ?>

    <?php // echo $form->field($model, 'flex_string2') ?>

    <?php // echo $form->field($model, 'flex_string2_label') ?>

    <?php // echo $form->field($model, 'bytes_in') ?>

    <?php // echo $form->field($model, 'bytes_out') ?>

    <?php // echo $form->field($model, 'message') ?>

    <?php // echo $form->field($model, 'event_outcome') ?>

    <?php // echo $form->field($model, 'transport_protocol') ?>

    <?php // echo $form->field($model, 'reason') ?>

    <?php // echo $form->field($model, 'request_url') ?>

    <?php // echo $form->field($model, 'request_client_application') ?>

    <?php // echo $form->field($model, 'request_context') ?>

    <?php // echo $form->field($model, 'request_cookies') ?>

    <?php // echo $form->field($model, 'request_method') ?>

    <?php // echo $form->field($model, 'device_receipt_time') ?>

    <?php // echo $form->field($model, 'source_host_name') ?>

    <?php // echo $form->field($model, 'source_mac_address') ?>

    <?php // echo $form->field($model, 'source_nt_domain') ?>

    <?php // echo $form->field($model, 'source_dns_domain') ?>

    <?php // echo $form->field($model, 'source_service_name') ?>

    <?php // echo $form->field($model, 'source_translated_address') ?>

    <?php // echo $form->field($model, 'source_translated_port') ?>

    <?php // echo $form->field($model, 'source_process_id') ?>

    <?php // echo $form->field($model, 'source_user_privileges') ?>

    <?php // echo $form->field($model, 'source_process_name') ?>

    <?php // echo $form->field($model, 'source_port') ?>

    <?php // echo $form->field($model, 'source_address') ?>

    <?php // echo $form->field($model, 'source_user_id') ?>

    <?php // echo $form->field($model, 'source_user_name') ?>

    <?php // echo $form->field($model, 'source_group_id') ?>

    <?php // echo $form->field($model, 'source_group_name') ?>

    <?php // echo $form->field($model, 'start_time') ?>

    <?php // echo $form->field($model, 'agent_translated_zone_key') ?>

    <?php // echo $form->field($model, 'agent_zone_key') ?>

    <?php // echo $form->field($model, 'customer_key') ?>

    <?php // echo $form->field($model, 'destination_translated_zone_key') ?>

    <?php // echo $form->field($model, 'destination_zone_key') ?>

    <?php // echo $form->field($model, 'device_translated_zone_key') ?>

    <?php // echo $form->field($model, 'device_zone_key') ?>

    <?php // echo $form->field($model, 'source_translated_zone_key') ?>

    <?php // echo $form->field($model, 'source_zone_key') ?>

    <?php // echo $form->field($model, 'reported_duration') ?>

    <?php // echo $form->field($model, 'reported_resource_group_name') ?>

    <?php // echo $form->field($model, 'reported_resource_id') ?>

    <?php // echo $form->field($model, 'reported_resource_name') ?>

    <?php // echo $form->field($model, 'reported_resource_type') ?>

    <?php // echo $form->field($model, 'framework_name') ?>

    <?php // echo $form->field($model, 'threat_actor') ?>

    <?php // echo $form->field($model, 'threat_attack_id') ?>

    <?php // echo $form->field($model, 'attack_type') ?>

    <?php // echo $form->field($model, 'source_country') ?>

    <?php // echo $form->field($model, 'source_city') ?>

    <?php // echo $form->field($model, 'destination_country') ?>

    <?php // echo $form->field($model, 'destination_city') ?>

    <?php // echo $form->field($model, 'source_geo_longitude') ?>

    <?php // echo $form->field($model, 'source_geo_latitude') ?>

    <?php // echo $form->field($model, 'destination_geo_longitude') ?>

    <?php // echo $form->field($model, 'destination_geo_latitude') ?>

    <?php // echo $form->field($model, 'destination_ip_network_model') ?>

    <?php // echo $form->field($model, 'source_ip_network_model') ?>

    <?php // echo $form->field($model, 'source_code') ?>

    <?php // echo $form->field($model, 'destination_code') ?>

    <?php // echo $form->field($model, 'parent_events') ?>

    <?php // echo $form->field($model, 'analyzed')->checkbox() ?>

    <?php // echo $form->field($model, 'cef_extensions') ?>

    <?php // echo $form->field($model, 'raw_event') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
