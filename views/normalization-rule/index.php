<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SecRule\SecRuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Normalization Rules';
?>

<div class="normalization-rule-index">

    <div class="main-actions centered-horizontal">
        <?= Html::a("<i class='material-icons'>add</i>" . Yii::t('app', 'Create Normalization Rule'), ['create'], ['class' => 'btn-floating waves-effect waves-light btn-large red']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            'type',
            [
                'attribute' => 'state',
                'label' => 'State',
                'value' => function($model){
                    if($model->state){
                        return 'ACTIVE';
                    }
                    else{
                        return 'INACTIVE';
                    }
                },
            ],
            ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn'],
        ],
    ]); ?>



</div>