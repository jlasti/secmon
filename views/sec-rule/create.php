<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SecRule */

<<<<<<< HEAD
$this->title = 'Create Sec Rule';
$this->params['breadcrumbs'][] = ['label' => 'Sec Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sec-rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

=======
$this->params['title'] = 'Create Sec Rule';
?>
<div class="sec-rule-create">

>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
