<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->params['title'] = 'Update user: ' . $model->username;
?>

<div class="user-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
