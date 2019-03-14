<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\EventsNormalized */

$this->title = 'Create Events Normalized';
$this->params['breadcrumbs'][] = ['label' => 'Events Normalizeds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="events-normalized-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
