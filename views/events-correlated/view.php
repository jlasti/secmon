<?php

use macgyer\yii2materializecss\widgets\data\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\EventsCorrelated */

$this->params['title'] = 'Correlated Event ID: ' . $model->id;

?>
<div class="events-correlated-view">

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
            'parent_events',
            'raw:ntext',
        ],
    ]) ?>

</div>
