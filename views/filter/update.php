<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Filter */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Filter',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Filters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="filter-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'rules' => $rules,
    ]) ?>

</div>
