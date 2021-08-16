<?php

use macgyer\yii2materializecss\widgets\data\DetailView;
use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\Interfaces;
use yii\bootstrap\Collapse;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Interafces */

$this->params['title'] = 'Interface ID: ' . $model->id;

?>
<div class="interfaces-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'network_model_id', 
            'ip_address', 
            'mac_address', 
            'name', 
        ],
    ]) ?>
</div>





