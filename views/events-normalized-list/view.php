<?php

use macgyer\yii2materializecss\widgets\data\DetailView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EventsNormalized */

$this->params['title'] = 'Normalized Event ID: ' . $model->id;

?>
<div class="main-actions centered-horizontal">
    <?= Html::a("<i class='material-icons'>delete</i>" . Yii::t('app', 'Delete'),
        ['delete', 'id' => $model->id],
        ['class' => 'btn-floating waves-effect waves-light btn-large red',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
</div>
<div class="events-normalized-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'datetime',
            'host',
            'cef_version',
            'cef_vendor',
            'cef_dev_prod',
            'cef_dev_version',
            'cef_event_class_id',
            'cef_name',
            'cef_severity',
            'src_ip',
            'dst_ip',
            'src_port',
            'dst_port',
            'protocol',
            'src_mac',
            'dst_mac',
            'src_code',
            'dst_code',
            'src_country',
            'dst_country',
            'src_city',
            'dst_city',
            'src_latitude',
            'dst_latitude' ,
            'src_longitude',
	        'dst_longitude',
	        'request_method',
	        'request_url:ntext',
	        'request_client_request:ntext',
            'destination_user_name',
            'destination_user_id',
            'destination_group_name',
            'destination_group_id',
            'device_process_id',
            'source_user_privileges',
            'exec_user',
            'extensions:ntext',
            'raw:ntext',
        ],
    ]) ?>
</div>
