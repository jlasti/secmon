<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SecurityEvents */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="security-events-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'datetime')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'cef_version')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cef_severity')->textInput() ?>

    <?= $form->field($model, 'cef_event_class_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cef_device_product')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cef_vendor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cef_device_version')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cef_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_action')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'application_protocol')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_ipv6_address1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_ipv6_address1_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_ipv6_address2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_ipv6_address2_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_ipv6_address3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_ipv6_address3_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_ipv6_address4')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_ipv6_address4_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_event_category')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_customfloating_point1')->textInput() ?>

    <?= $form->field($model, 'device_customfloating_point1_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_customfloating_point2')->textInput() ?>

    <?= $form->field($model, 'device_customfloating_point2_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_customfloating_point3')->textInput() ?>

    <?= $form->field($model, 'device_customfloating_point3_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_customfloating_point4')->textInput() ?>

    <?= $form->field($model, 'device_customfloating_point4_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_number1')->textInput() ?>

    <?= $form->field($model, 'device_custom_number1_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_number2')->textInput() ?>

    <?= $form->field($model, 'device_custom_number2_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_number3')->textInput() ?>

    <?= $form->field($model, 'device_custom_number3_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'baseEventCount')->textInput() ?>

    <?= $form->field($model, 'device_custom_string1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_string1_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_string2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_string2_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_string3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_string3_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_string4')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_string4_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_string5')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_string5_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_string6')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_string6_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_date1')->textInput() ?>

    <?= $form->field($model, 'device_custom_date1_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_custom_date2')->textInput() ?>

    <?= $form->field($model, 'device_custom_date2_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_direction')->textInput() ?>

    <?= $form->field($model, 'device_dns_domain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_external_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_facility')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_inbound_interface')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_nt_domain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_outbound_interface')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_payload_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_process_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_translated_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_time_zone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_host_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_mac_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_process_id ')->textInput() ?>

    <?= $form->field($model, 'destination_host_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_mac_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_nt_domain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_dns_domain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_service_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_translated_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_translated_port')->textInput() ?>

    <?= $form->field($model, 'destination_process_id')->textInput() ?>

    <?= $form->field($model, 'destination_user_privileges')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_process_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_port')->textInput() ?>

    <?= $form->field($model, 'destination_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_group_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_group_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'end_time')->textInput() ?>

    <?= $form->field($model, 'external_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'file_create_time')->textInput() ?>

    <?= $form->field($model, 'file_hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'file_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'file_modification_time')->textInput() ?>

    <?= $form->field($model, 'file_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'file_path')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'file_permission')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'file_size')->textInput() ?>

    <?= $form->field($model, 'file_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'old_file_create_time')->textInput() ?>

    <?= $form->field($model, 'old_file_hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'old_file_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'old_file_modification_time')->textInput() ?>

    <?= $form->field($model, 'old_file_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'old_file_path')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'old_file_permission')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'old_file_size')->textInput() ?>

    <?= $form->field($model, 'old_file_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'flex_date1')->textInput() ?>

    <?= $form->field($model, 'flex_date1_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'flex_string1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'flex_string1_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'flex_string2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'flex_string2_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bytes_in')->textInput() ?>

    <?= $form->field($model, 'bytes_out')->textInput() ?>

    <?= $form->field($model, 'message')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'event_outcome')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'transport_protocol')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'request_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'request_client_application')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'request_context')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'request_cookies')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'request_method')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_receipt_time')->textInput() ?>

    <?= $form->field($model, 'source_host_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_mac_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_nt_domain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_dns_domain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_service_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_translated_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_translated_port')->textInput() ?>

    <?= $form->field($model, 'source_process_id')->textInput() ?>

    <?= $form->field($model, 'source_user_privileges')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_process_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_port')->textInput() ?>

    <?= $form->field($model, 'source_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_group_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_group_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'start_time')->textInput() ?>

    <?= $form->field($model, 'agent_translated_zone_key')->textInput() ?>

    <?= $form->field($model, 'agent_zone_key')->textInput() ?>

    <?= $form->field($model, 'customer_key')->textInput() ?>

    <?= $form->field($model, 'destination_translated_zone_key')->textInput() ?>

    <?= $form->field($model, 'destination_zone_key')->textInput() ?>

    <?= $form->field($model, 'device_translated_zone_key')->textInput() ?>

    <?= $form->field($model, 'device_zone_key')->textInput() ?>

    <?= $form->field($model, 'source_translated_zone_key')->textInput() ?>

    <?= $form->field($model, 'source_zone_key')->textInput() ?>

    <?= $form->field($model, 'reported_duration')->textInput() ?>

    <?= $form->field($model, 'reported_resource_group_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reported_resource_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reported_resource_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reported_resource_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'framework_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'threat_actor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'threat_attack_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'attack_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_geo_longitude')->textInput() ?>

    <?= $form->field($model, 'source_geo_latitude')->textInput() ?>

    <?= $form->field($model, 'destination_geo_longitude')->textInput() ?>

    <?= $form->field($model, 'destination_geo_latitude')->textInput() ?>

    <?= $form->field($model, 'destination_ip_network_model')->textInput() ?>

    <?= $form->field($model, 'source_ip_network_model')->textInput() ?>

    <?= $form->field($model, 'source_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'destination_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'parent_events')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'analyzed')->checkbox() ?>

    <?= $form->field($model, 'cef_extensions')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'raw_event')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
