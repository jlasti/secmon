<?php

/* @var $this yii\web\View */
/* @var $model app\models\NormalizationRule */

$this->params['title'] = 'Add Normalization Rule';
?>

<div class="normalization-rule-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>