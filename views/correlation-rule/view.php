<?php

use macgyer\yii2materializecss\widgets\data\DetailView;


$this->params['title'] = 'Correlation Rule: ' . $model->ruleFileName;
?>

<div class="sec-rule-view">

    <div class="main-actions centered-horizontal">
        <a href="." class="btn grey darken-2" style="border-radius: 10px">Close</a>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
                'label' => 'Latest modification time',
            ],
            [
                'attribute' => 'accessed_at',
                'label' => 'Latest access time',
            ],
            [
                'attribute' => 'size',
                'label' => 'Size (B)',
            ],
            [
                'attribute' => 'uid',
                'label' => 'User ID of owner',
            ],
            [
                'attribute' => 'gid',
                'label' => 'Group ID of owner',
            ],
        ],
    ]) ?>

</div>