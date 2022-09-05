<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SecurityEvents */

$this->title = 'Create Security Events';
$this->params['breadcrumbs'][] = ['label' => 'Security Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="security-events-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
