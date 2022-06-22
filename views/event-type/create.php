<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Event\EventType */

$this->params['title'] = 'Create New Event Type';
?>
<div class="event-type-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
