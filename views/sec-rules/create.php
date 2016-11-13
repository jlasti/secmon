<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SecRules */

$this->title = 'Create Sec Rules';
$this->params['breadcrumbs'][] = ['label' => 'Sec Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sec-rules-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
