<?php

use macgyer\yii2materializecss\widgets\data\DetailView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EventsCorrelated */

$this->params['title'] = 'Correlated Event ID: ' . $model->id;

?>

<div class="main-actions centered-horizontal">
   <?= Html::a("<i class='material-icons' title=\"Delete event\">delete</i>" . Yii::t('app', 'Delete'),
        ['delete', 'id' => $model->id],
        ['class' => 'btn-floating waves-effect waves-light btn-large red',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
</div>
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
            'attack_type',
            'raw:ntext',
        ],
    ]) ?>

</div>
