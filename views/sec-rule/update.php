<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SecRule */

$this->params['title'] = 'Update Sec Rule: ' . $model->name;
?>
<div class="sec-rule-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
