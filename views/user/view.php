<?php

use yii\helpers\Html;
use app\components\PresenterDetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->params['title'] = 'model->id';
?>
<div class="user-view">

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
            'first_name',
            'last_name',
            'username',
            //'password',
            'email:email',
            [
            	'attribute' => 'roles',
				'format' => 'raw',
			],
            //'auth_key',
        ],
    ]) ?>

</div>
