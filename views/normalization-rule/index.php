<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\grid\GridView;

$this->params['title'] = 'Normalization Rules';
?>

<div class="normalization-rule-index">

    <!-- <div class="main-actions centered-horizontal">
        <?= Html::a("<i class='material-icons'>add</i>" . Yii::t('app', 'Create Normalization Rule'), ['create'], ['class' => 'btn-floating waves-effect waves-light btn-large red']) ?>
    </div> -->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'label' => 'Rule name',
            ],
            [
                'attribute' => 'ruleFileName',
                'label' => 'Normalization rule file path',
            ],
            [
                'attribute' => 'active',
                'label' => 'Status',
                'format' => 'html',
                'value' => function ($model) {
                        if ($model->active == 1) {
                            return '<span style="color: #11ff00;">ACTIVE</span>';
                        } else {
                            return '<span style="color: red">INACTIVE</span>';
                        }
                    },
            ],
            [
                'attribute' => 'modified_at',
                'label' => 'Last modified',
            ],
            [
                'attribute' => 'size',
                'label' => 'Size (B)',
            ],
            [
                'class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'view') {
                            $url = ['view', 'ruleFileName' => $model->ruleFileName];
                            return $url;
                        }
                        if ($action === 'update') {
                            $url = ['update', 'ruleFileName' => $model->ruleFileName];
                            return $url;
                        }
                        if ($action === 'delete') {
                            $url = ['delete', 'ruleFileName' => $model->ruleFileName];
                            return $url;
                        }
                    }
            ],
        ],
    ]); ?>

    <div class='center' style="margin-top: 20px;">
        <?= Html::a('Add normalization rule', ['create'], ['class' => 'btn']) ?>
    </div>

</div>