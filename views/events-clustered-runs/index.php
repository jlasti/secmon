<?php

use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
use kartik\cmenu\ContextMenu;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EventsClusteredRunsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Clustered Events Runs';

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

<div class="main-actions centered-horizontal">
    <?= Html::a('miniSOM', ['/events-clustered-runs/minisom'], ['class' => 'btn', 'title' => 'miniSOM', 'style' => 'border-radius: 10px']) ?>
    <?= Html::a('k-median', ['/events-clustered-runs/kmedian'], ['class' => 'btn', 'title' => 'k-median', 'style' => ' border-radius: 10px']) ?>
</div>

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
                        'attribute' => 'datetime',
                        'value' => 'datetime',
                        'format' => 'raw',
                        'filter' => \macgyer\yii2materializecss\widgets\form\DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'datetime',
                            'clientOptions' => [
                                'format' => 'yyyy-mm-dd'
                            ]
                        ])
                    ],
                    'id',
                    'type_of_algorithm',
                    ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn', 'template'=>'{view}'],
                ],
            ]); ?>
    <?php Pjax::end(); ?>
</div>


