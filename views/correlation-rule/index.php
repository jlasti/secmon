<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\grid\GridView;

$this->params['title'] = 'Correlation Rules';
?>

<div class="sec-rule-index">
    
    <div class="main-actions centered-horizontal">
        <?= Html::a(Yii::t('app', 'Import new rule'), ['create'], ['class' => 'btn red', 'style' => 'border-radius: 10px']) ?>
        <?= Html::a(Yii::t('app', 'Update rules'), ['rules-update'], ['class' => 'btn', 'style' => 'border-radius: 10px']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'label' => 'Custom rule name',
            ],
            [
                'attribute' => 'ruleFileName',
                'label' => 'Rule file name',
            ],
            [
                'attribute' => 'active',
                'label' => 'State',
                'format' => 'html',
                'value' => function ($model) {
                        if ($model->active == 1) {
                            return '<span style="font-weight: bold; color: #00c853;">ACTIVE</span>';
                        } else {
                            return '<span style="color: #2196f3">INACTIVE</span>';
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

</div>