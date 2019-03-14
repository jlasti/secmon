<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Role */

$this->params['title'] = 'Create Role';
?>
<div class="role-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
