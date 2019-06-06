<?php

use macgyer\yii2materializecss\widgets\form\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NormalizationRule */
/* @var $form yii\widgets\ActiveForm */

$this->params['title'] = 'Update Normalization Rule: ' . $model->name;

?>
<div class="normalization-rule-update">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'state')->checkbox() ?>
    </div>

    <div class="row"></div>

    <div class="row">
        <div class="form-group">
            <?=Html::submitButton('Update', ['class' => 'waves-effect waves-light btn'])?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>