<?php

use macgyer\yii2materializecss\widgets\data\DetailView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SecurityEvents */

$this->params['title'] = 'Security Event ID: ' . $model->id;

?>

<div class="security-events-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'datetime',
            'device_host_name',
            'cef_version',
            'cef_vendor',
            'cef_device_product',
            'cef_device_version',
            'cef_event_class_id',
            'cef_name',
            'cef_severity',
            'source_address',
            'destination_address',
            'source_port',
            'destination_port',
            'protocol',
            'source_mac',
            'destination_mac',
            'source_code',
            'destination_code',
            'source_country',
            'destination_country',
            'source_city',
            'destination_city',
            'source_geo_latitude',
            'destination_geo_latitude' ,
            'source_geo_longitude',
	        'destination_geo_longitude',
	        'request_method',
	        'request_url:ntext',
	        'request_client_request:ntext',
            'destination_user_name',
            'destination_user_id',
            'destination_group_name',
            'destination_group_id',
            'device_process_id',
            'source_user_privileges',
            'extensions:ntext',
            'raw:ntext',
        ],
    ]) ?>
</div>
