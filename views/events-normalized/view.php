<?php

use macgyer\yii2materializecss\widgets\data\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\EventsNormalized */

$this->params['title'] = 'Normalized Event ID: ' . $model->id;

?>
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
            'extensions:ntext',
            'raw:ntext',
        ],
    ]) ?>

</div>
