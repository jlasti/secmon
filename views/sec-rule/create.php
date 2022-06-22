<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SecRule */

$this->params['title'] = 'Create Correlation Rule';
?>
<div class="sec-rule-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
