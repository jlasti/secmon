<?php

use macgyer\yii2materializecss\widgets\form\ActiveForm;


$this->params['title'] = 'Update Normalization Rule: ' . $model->ruleFileName;
?>

<div class="normalization-rule-update">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="main-actions centered-horizontal">
        <button type="submit" class="btn red" style="border-radius: 10px">Update</button>
        <a href="." class="btn grey darken-2" style="border-radius: 10px">Close</a>
    </div>


    <div class="row" style="margin-bottom: -10px;">
        <div style="margin-bottom: -10px;">
            <label style="font-size: larger;">Description</label>
        </div>
        <div style='margin-top: -20px;'>
            <?= $form->field($model, 'description')->textInput(['maxlength' => true])->label(false) ?>
        </div>
    </div>

    <div class="row" style="margin-bottom: 5px;">
        <div style="margin-bottom: -10px;">
            <label style="font-size: larger;">Rule state</label>
        </div>
        <div style='margin-left: 10px;'>
            <?= $form->field($model, 'active')->checkbox() ?>
        </div>
    </div>

    <div class="row"></div>

    <div style="margin-left: -10px;">
        <label style="font-size: larger;">Edit rule</label>
        <div style='border: 1px solid grey; padding: 0 5px; font-family: consolas;'>
            <?= $form->field($model, 'content')->textarea(['showCharacterCounter' => true])->label(false) ?>
        </div>
    </div>

    <div class="row"></div>

    <?php ActiveForm::end(); ?>
</div>