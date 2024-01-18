<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NormalizationRule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="normalization-rule-form">

    <?php $form = ActiveForm::begin(); ?>

    <div>
        <?= $form->field($model, 'name')->textInput(['maxlength' => 50]) ?>
    </div>

    <div style='margin-bottom: 50px;'>
        <label>Rule state</label>
        <div style="margin-left: 5px;">
            <?= $form->field($model, 'active')->checkbox() ?>
        </div>
    </div>

    <div class="row"></div>

    <div class='row' style='margin-left: 0px;'>
        <div class='form-input'>
            <button type="submit" class="btn btn-primary">Add</button>
            <a href="." class="btn btn-primary grey">Close</a>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>