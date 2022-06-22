<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\form\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\NetworkModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="network-model-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <?= $form->field($model, 'hostname')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'criticality')->input('number', ['min' => 0, 'max' => 10])?>
    </div>

    <div class="row">
        <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'operation_system')->textInput(['maxlength' => true]) ?>
    </div>
			
    <div class="row">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'waves-effect waves-light green btn' : 'waves-effect waves-light btn']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
