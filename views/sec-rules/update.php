<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SecRules */

$this->title = 'Update Sec Rules: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Sec Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sec-rules-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
