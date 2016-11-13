<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Role */

$this->params['title'] = 'Update role: ' . $model->name;
?>

<div class="role-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
