<?php

use macgyer\yii2materializecss\widgets\data\DetailView;
use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\Interfaces;
use yii\bootstrap\Collapse;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\NetworkModel */

$this->params['title'] = 'Network model device ID: ' . $model->id;
$numOfInterfaces = Interfaces::getInterfacesByNetworkModel($model->id)->getTotalCount();
?>

<div class="main-actions centered-horizontal">
    <?= Html::a("<i class='material-icons' title=\"Create new Interface\">add</i>" . Yii::t('app', 'Create'), 
        ['create', 'id' => $model->id], 
        ['class' => 'btn-floating waves-effect waves-light btn-large red'
        ]) 
    ?>
    <?= Html::a("<i class='material-icons' title=\"Merge with existing network model device\">merge_type</i>" . Yii::t('app', 'Merge'), 
        ['merge', 'id' => $model->id], 
        ['class' => 'btn-floating waves-effect waves-light btn-large green']) 
    ?>

    <?= Html::a("<i class='material-icons' title=\"Edit network model device\">edit</i>" . Yii::t('app', 'Update'), 
        ['update', 'id' => $model->id], 
        ['class' => 'btn-floating waves-effect waves-light btn-large blue']) 
    ?>

    <?= Html::a("<i class='material-icons' title=\"Delete network model device\">delete</i>" . Yii::t('app', 'Delete'),
        ['delete', 'id' => $model->id],
        ['class' => 'btn-floating waves-effect waves-light btn-large red',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) 
    ?>
</div>
<div class="netwrok-model-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'ip_address', 
            'mac_address',
            'criticality', 
            'description', 
            'hostname', 
            'operation_system', 
            'open_ports', 
            'ports', 
            'services', 
            'vulnerabilities'
        ],
    ]) ?>
</div>

<ul class="collapsible">
    <li>
      <div class="collapsible-header light-blue accent-4" style="font-size:20px; color: white;"><i class="material-icons">filter_drama</i>Interfaces (<?= $numOfInterfaces ?>)</div>
      <div class="collapsible-body"><?= GridView::widget([
            'dataProvider' => Interfaces::getInterfacesByNetworkModel($model->id),
            'summary' => '',
            'columns' => [
                [
                        'class' => 'yii\grid\SerialColumn',
                ],
                'ip_address',
                'mac_address',
                'name',
                [
                    'class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn', 
                    'controller' => 'interfaces'
                ],
            ],
    ]); ?> </div>
    </li>
  </ul>