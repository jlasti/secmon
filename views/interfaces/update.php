<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Interafces */

$this->params['title'] = 'Update Interface: ' . $model->name;
?>
<div class="interface-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
