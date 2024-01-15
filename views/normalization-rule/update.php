<?php

use macgyer\yii2materializecss\widgets\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NormalizationRule */
/* @var $form yii\widgets\ActiveForm */

$this->params['title'] = 'Update Normalization Rule: ' . $model->name;

?>

<div class="normalization-rule-form">

    <?php $form = ActiveForm::begin(); ?>

    <div>
        <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
    </div>

    <div style='margin-bottom: 50px;'>
        <label>Rule state</label>
        <div style="margin-left: 5px;">
            <?= $form->field($model, 'active')->checkbox() ?>
        </div>
    </div>

    <label>Set normalization rule file path</label>

    <div class="row">
        <div class="file-field input-field" style="margin-left: 10px;">
            <div class="btn blue">
                <span>Set file path</span>
                <?= $form->field($model, 'normalizationRuleFile')->fileInput()->label(false) ?>
            </div>
            <div class="file-path-wrapper">
                <input class="file-path validate" type="text" placeholder="Provide path to normalization rule">
            </div>
            <?php if ($model->normalizationRuleFile): ?>
                <span class="text-muted">Current File: <a href="<?= $model->normalizationRuleFile ?>" target="_blank">
                        <?= basename($model->normalizationRuleFile) ?>
                    </a></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="row"></div>

    <div class='row' style='margin-left: 0px;'>
        <div class='form-input'>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="." class="btn btn-primary grey" >Close</a>
        </div> 
    </div>
    
    <?php ActiveForm::end(); ?>
</div>