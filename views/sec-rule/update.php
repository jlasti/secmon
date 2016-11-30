<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SecRule */

<<<<<<< HEAD
$this->title = 'Update Sec Rule: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Sec Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sec-rule-update">

    <h1><?= Html::encode($this->title) ?></h1>

=======
$this->params['title'] = 'Update Sec Rule: ' . $model->name;
?>
<div class="sec-rule-update">

>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
