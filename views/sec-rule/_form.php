<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\form\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\SecRule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sec-rule-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'secConfigFile')->fileInput() ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'state')->checkbox() ?>
    </div>

    <div class="row"></div>
			
    <div class="row">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'waves-effect waves-light green btn' : 'waves-effect waves-light btn']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
