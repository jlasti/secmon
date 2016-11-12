<?php

use app\components\PresenterDetailView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Role */

$this->params['title'] = $model->name;
?>
<div class="role-view">

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

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
