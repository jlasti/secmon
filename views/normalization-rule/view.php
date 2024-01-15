<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\data\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\NormalizationRule */

$this->params['title'] = 'Normalization Rule: ' . $model->name;

?>

<div class="normalization-rule-view">

    <div class="main-actions centered-horizontal">
        <?= Html::a("<i class='material-icons'>edit</i>" . Yii::t('app', 'Update'), ['update', 'name' => $model->name], ['class' => 'btn-floating waves-effect waves-light btn-large blue']) ?>
        <?= Html::a(
            "<i class='material-icons'>delete</i>" . Yii::t('app', 'Delete'),
            ['delete', 'id' => $model->id],
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
                'attribute' => 'normalizationRuleFile',
                'label' => 'Normalization rule file path',
            ],
            [
                'attribute' => 'uiFileName',
                'label' => 'UI File name',
            ],
            'id',
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
                'attribute' => 'created_at',
                'label' => 'Created at',
                'value' => function ($model) {
                return $model->getPrettyDateTime('created_at');
            },
            ],
            [
                'attribute' => 'modified_at',
                'label' => 'Last modified',
                'value' => function ($model) {
                return $model->getPrettyDateTime('modified_at');
            },
            ],
        ],
    ])
        ?>

    <div class='center'>
        <a href="." class="btn btn-primary grey" >Close</a>
    </div>

</div>