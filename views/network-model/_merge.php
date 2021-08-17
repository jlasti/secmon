<?php

use macgyer\yii2materializecss\widgets\data\DetailView;
use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\NetworkModel;
use yii\bootstrap\Collapse;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\NetworkModel */

$this->params['id'] = $id;
$this->params['title'] = 'Merge with existing network model device';
?>
<?= GridView::widget([
                'dataProvider' => NetworkModel::getNetworkDevices($id),
                'summary' => '',
                'columns' => [
                    [
                            'class' => 'yii\grid\SerialColumn',
                    ],
                    'ip_address',
                    'mac_address',
                    'hostname',
                    'description',
                    [
                        'class' => 'yii\grid\CheckboxColumn'
                    ],
                    [
                        'class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn', 
                        'template' => '{merge}',
                        'buttons' => [
                            'merge' => function($url, $model){
                                return Html::a(
                                    '<i class="material-icons">merge_type</i>',
                                    Url::to(['network-model/processselected', 'id' => $this->params['id'], 'del_id' => $model->id]));
                            }
                        ],
                    ],
                ],
        ]);
    ?>