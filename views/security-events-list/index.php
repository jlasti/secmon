<?php

use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\AnalyzedSecurityEventsListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Analyzed Security Events List';

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
<div class="security-events-index clickable-table">
    <?php Pjax::begin(['id' => 'pjaxContainer']); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => '{items}<div id="pagination">{pager}</div>',
            'tableOptions' => [
                'analyzed_security_events_id' => 'eventsContent',
                'class' => 'responsive-table striped'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'securityEvent.datetime',
                'securityEvent.cef_name',
                'securityEvent.device_host_name',
                'securityEvent.source_address',
                'securityEvent.destination_address',
                'securityEvent.source_country',
                'securityEvent.destination_country',
                'securityEvent.source_code',
                'securityEvent.destination_code',

                ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn', 'template'=>'{view}'],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
