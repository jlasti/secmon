<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Event\EventType */

$this->params['title'] = 'Update type: ' . $model->name;
?>

<div class="event-type-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
