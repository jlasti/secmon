<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\View */

$this->params['title'] = 'Update: ' . $model->name;
?>
<div class="view-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
