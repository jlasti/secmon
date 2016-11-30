<?php

use yii\helpers\Html;
<<<<<<< HEAD
use yii\widgets\ActiveForm;
=======
use macgyer\yii2materializecss\widgets\form\ActiveForm;
>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159

/* @var $this yii\web\View */
/* @var $model app\models\SecRule\SecRuleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sec-rule-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'content') ?>

    <div class="form-group">
<<<<<<< HEAD
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
=======
        <?= Html::submitButton('Search', ['class' => 'waves-effect waves-light btn']) ?>
        <?= Html::resetButton('Reset', ['class' => 'waves-effect waves-light red btn']) ?>
>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159
    </div>

    <?php ActiveForm::end(); ?>

</div>
