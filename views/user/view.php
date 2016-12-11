<?php

use yii\helpers\Html;
use app\components\PresenterDetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->params['title'] = 'User: ' . $model->username;
?>
<div class="user-view">

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
