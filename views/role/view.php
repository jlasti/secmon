<?php

use app\components\PresenterDetailView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Role */

$this->params['title'] = $model->name;
?>
<div class="role-view">
    
    <div class="main-actions centered-horizontal">
        <?= Html::a("<i class='material-icons'>edit</i>" . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-floating waves-effect waves-light btn-large blue']) ?>
        <?= Html::a("<i class='material-icons'>delete</i>" . Yii::t('app', 'Delete'), 
            ['delete', 'id' => $model->id],
            ['class' => 'btn-floating waves-effect waves-light btn-large red',
             'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>
    <?= PresenterDetailView::widget([
        'model' => $model->presenter(),
        'attributes' => [
            //'id',
            'name',
            //'slug',
			[
				'attribute' => 'permissions',
				'format' => 'raw',
			],
        ],
    ]) ?>

</div>
