<?php

use yii\helpers\Html;
<<<<<<< HEAD
use yii\widgets\DetailView;
=======
use macgyer\yii2materializecss\widgets\data\DetailView;
>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159

/* @var $this yii\web\View */
/* @var $model app\models\SecRule */

<<<<<<< HEAD
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Sec Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sec-rule-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
=======
$this->params['title'] = 'Sec Rule: ' . $model->name;
?>
<div class="sec-rule-view">

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
>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'content',
        ],
    ]) ?>

</div>
