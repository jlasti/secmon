<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Event\Event */

$this->params['title'] = 'Update event: ' . $model->title;
?>

<div class="event-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
