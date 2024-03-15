<?php

$this->params['title'] = 'Add Normalization Rule';
?>

<div class="normalization-rule-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
    
</div>