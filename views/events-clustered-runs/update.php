<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EventsClusteredRuns */

$this->title = 'Update Events Clustered Runs: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Events Clustered Runs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="events-clustered-runs-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
