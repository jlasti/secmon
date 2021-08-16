<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Interface*/

$this->params['title'] = 'Create new interface';
?>
<div class="interface-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
