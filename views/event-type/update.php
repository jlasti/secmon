<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Event\EventType */

<<<<<<< HEAD
$this->params['title'] = 'Update type: ' . $model->name;
=======
$this->params['title'] = 'Update event type: ' . $model->name;
>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159
?>

<div class="event-type-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
