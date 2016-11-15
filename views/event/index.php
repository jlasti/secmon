<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\Event\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Events';
?>
<div class="event-index">
    <?php Pjax::begin(); ?>    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'title',
                'description:ntext',
                'timestamp',
                'type_id',

                ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn'],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
