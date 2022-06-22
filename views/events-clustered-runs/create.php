<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\EventsClusteredRuns */

$this->title = 'Create Events Clustered Runs';
$this->params['breadcrumbs'][] = ['label' => 'Events Clustered Runs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="events-clustered-runs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
