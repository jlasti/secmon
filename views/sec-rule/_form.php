<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\form\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\SecRule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sec-rule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'state')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'waves-effect waves-light green btn' : 'waves-effect waves-light btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
