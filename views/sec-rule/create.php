<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SecRule */

$this->title = 'Create Sec Rule';
$this->params['breadcrumbs'][] = ['label' => 'Sec Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sec-rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
