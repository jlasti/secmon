<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Event\Event */

$this->params['title'] = 'Create Event';
?>
<div class="event-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
