<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Filter */

$this->params['title'] = 'Create Filter';
?>
<div class="filter-create">

    <?= $this->render('_form', [
        'model' => $model,
        'rules' => $rules,
    ]) ?>

</div>
