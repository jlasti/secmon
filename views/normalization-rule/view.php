<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\data\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\NormalizationRule */

$this->params['title'] = 'Normalization Rule: ' . $model->name;

?>

<div class="normalization-rule-view">

    <div class="main-actions centered-horizontal">
        <?= Html::a("<i class='material-icons'>edit</i>" . Yii::t('app', 'Update'), ['update', 'ruleFileName' => $model->ruleFileName], ['class' => 'btn-floating waves-effect waves-light btn-large blue']) ?>
        <?= Html::a(
            "<i class='material-icons'>delete</i>" . Yii::t('app', 'Delete'),
            ['delete', 'ruleFileName' => $model->ruleFileName],
            [
                'class' => 'btn-floating waves-effect waves-light btn-large red',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]
        ) ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'name',
                'label' => 'Rule name',
            ],
            [
                'attribute' => 'ruleFileName',
                'label' => 'Rule file name',
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
        ],
    ]) ?>

    <div class='center'>
        <a href="." class="btn btn-primary grey">Close</a>
    </div>

</div>