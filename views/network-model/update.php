<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NetworkModel */

$this->params['title'] = 'Update network model device: ' . $model->hostname;
?>
<div class="sec-rule-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>