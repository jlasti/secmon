<?php

use yii\helpers\Html;
<<<<<<< HEAD
use yii\widgets\ActiveForm;

=======
use macgyer\yii2materializecss\widgets\form\ActiveForm;
>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159
/* @var $this yii\web\View */
/* @var $model app\models\SecRule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sec-rule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
<<<<<<< HEAD
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
=======
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'waves-effect waves-light green btn' : 'waves-effect waves-light btn']) ?>
>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159
    </div>

    <?php ActiveForm::end(); ?>

</div>
