<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\View */

$this->params['title'] = 'Create Dashboard';
?>
<div class="view-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
