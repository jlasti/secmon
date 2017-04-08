<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EventsNormalized */

$this->title = 'Update Events Normalized: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Events Normalizeds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="events-normalized-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
