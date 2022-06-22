<?php

use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
use kartik\cmenu\ContextMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EventsClusteredSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Clustered Events';

$this->registerJs('
    setInterval(function() {
        $.pjax.reload({
            container:"#pjaxContainer table#eventsContent tbody:last", 
            fragment:"table#eventsContent tbody:last"})
            .done(function() {
                activateEventsRows();
                $.pjax.reload({
                    container:"#pjaxContainer #pagination", 
                    fragment:"#pagination"
                });
            });
    }, 5000);
');

?>
<div class="events-normalized-index clickable-table">
    <?php Pjax::begin(['id' => 'pjaxContainer']); ?>
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
                    [
                        'attribute' => 'time',
                        'value' => 'time',
                        'format' => 'raw',
                        'filter' => \macgyer\yii2materializecss\widgets\form\DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'time',
                            'clientOptions' => [
                                'format' => 'yyyy-mm-dd'
                            ]
                        ])
                    ],
                    'cluster_run',
                    'cluster_number',
                    'raw',
                ],
            ]); ?>
    <?php Pjax::end(); ?>
</div>


