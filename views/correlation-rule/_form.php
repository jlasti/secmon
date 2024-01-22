<?php

use macgyer\yii2materializecss\widgets\form\ActiveForm;


?>

<div class="sec-rule-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="main-actions centered-horizontal">
        <button type="submit" class="btn red" style="border-radius: 10px">Import new rule</button>
        <a href="." class="btn grey darken-2" style="border-radius: 10px">Close</a>
    </div>

    <div class="row" style="margin-bottom: -10px;">
        <div style="margin-bottom: -10px;">
            <label style="font-size: larger;">URL path</label>
        </div>
        <div style='margin-top: -20px;'>
            <?= $form->field($model, 'content')->textInput(['maxlength' => 150])->label(false) ?>
        </div>
    </div>

    <div class="row" style="margin-bottom: 15px;">
        <div style="margin-bottom: -10px;">
            <label style="font-size: larger;">Custom rule file name</label>
        </div>
        <div style='margin-top: -20px;'>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label(false) ?>
        </div>
        <div style="display: flex; align-items: center; margin-top: -12px;">
            <i class='material-icons' style='color: #42a5f5; margin-right: 5px;'>info</i>
            <span style="font-weight:; color: #42a5f5; ">If no file name is set, file name would be extracted from
                URL</span>
        </div>
    </div>

    <div class="row" style="margin-bottom: 5px;">
        <div style="margin-bottom: -10px;">
            <label style="font-size: larger;">Rule state</label>
        </div>
        <div style="margin: -15px 0 0 5px;">
            <?= $form->field($model, 'active')->checkbox() ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>