<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\EventsCorrelated */

$this->title = 'Create Events Correlated';
$this->params['breadcrumbs'][] = ['label' => 'Events Correlateds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="events-correlated-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
