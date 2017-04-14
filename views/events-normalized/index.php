<?php

use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EventsNormalizedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Normalized Events';

?>
<div class="events-normalized-index clickable-table">

    <?php Pjax::begin(['id' => 'pjaxContainer']); ?>    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                // 'id',
                'datetime',
                'host',
                // 'cef_version',
                // 'cef_vendor',
                // 'cef_dev_prod',
                // 'cef_dev_version',
                // 'cef_event_class_id',
                'cef_name',
                'cef_severity',
                'src_ip',
                'dst_ip',
                'src_port',
                'dst_port',
                'protocol',
                'src_mac',
                'dst_mac',
                // 'extensions:ntext',
                // 'raw:ntext',

                ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn', 'template'=>'{view}'],
            ],
        ]); ?>
    <?php Pjax::end(); ?>

    <?php
      $this->registerJs('
        setInterval(
          function() {
            $.pjax.reload({container:"#pjaxContainer"});
          }
          , 5000
        );', \yii\web\VIEW::POS_HEAD);
    ?>
</div>
