<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Filter */

$this->params['title'] = 'Update Filter: ' . $model->name;
?>
<div class="filter-update">

    <?= $this->render('_form', [
        'model' => $model,
        'rules' => $rules,
    ]) ?>

</div>
