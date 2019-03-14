<?php

use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EventsAnalyzedNormalizedListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Analyzed Normalized Events List';

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
<div class="events-correlated-index clickable-table">
    <?php Pjax::begin(['id' => 'pjaxContainer']); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => '{items}<div id="pagination">{pager}</div>',
            'tableOptions' => [
                'events_analyzed_normalized_id' => 'eventsContent',
                'class' => 'responsive-table striped'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'eventNormalized.datetime',
                'eventNormalized.cef_name',
                'eventNormalized.host',
                'eventNormalized.src_ip',
                'eventNormalized.dst_ip',
                'eventNormalized.src_country',
                'eventNormalized.dst_country',
                'eventNormalized.src_code',
                'eventNormalized.dst_code',

                ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn', 'template'=>'{view}'],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
