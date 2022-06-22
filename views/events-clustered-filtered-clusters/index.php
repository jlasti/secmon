<?php

use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
use kartik\cmenu\ContextMenu;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EventsClusteredFilteredClustersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Clustered Events Event ID: ' . preg_replace('/[^0-9]/','',$_GET['event_id']);

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
                        'fk_run_id',
                        'id',
                        [
                        'attribute' => 'severity',
                        'value' => 'severity',
                        'contentOptions' => function ($dataProvider, $key, $index, $column) {
                            $array = [
                                ['id' => '1', 'data' => '#00DBFF'],
                                ['id' => '2', 'data' => '#00DBFF'],
                                ['id' => '3', 'data' => '#00FF00'],
                                ['id' => '4', 'data' => '#00FF00'],
                                ['id' => '5', 'data' => '#FFFF00'],
                                ['id' => '6', 'data' => '#FFFF00'],
                                ['id' => '7', 'data' => '#CC5500'],
                                ['id' => '8', 'data' => '#CC5500'],
                                ['id' => '9', 'data' => '#FF0000'],
                                ['id' => '10', 'data' => '#FF0000'], 
                            ];
                            if (0 < $dataProvider->severity && $dataProvider->severity < 11){
                                $map = ArrayHelper::map($array, 'id', 'data');
                                return ['style' => 'background-color:'.$map[$dataProvider->severity]];
                            } else {
                                return ['style' => 'background-color:#FFFFFF'];
                            }
                        }
                    ],
                        'number_of_events',
                        'comment',
                        ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn', 'template'=>'{update}{view}'],
                    ],
            ]); ?>
    <?php Pjax::end(); ?>
