<?php

use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
use kartik\cmenu\ContextMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NetworkModelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Network model';
?>


<div class="network-model-index clickable-table">
    <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'layout' => '{items}<div id="pagination">{pager}</div>',
                'tableOptions' => [
                    'id' => 'eventsContent',
                    'class' => 'responsive-table striped'
                ],
                'columns' => [
                    [
                            'class' => 'yii\grid\SerialColumn',
                    ],
                    'ip_address',
                    'mac_address',
                    'description',
                    'hostname',
                    'operation_system',
                    ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn', 'template'=>'{view}'],
                ],
            ]); ?>
    <?php Pjax::end(); ?>
</div>