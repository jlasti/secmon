<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NormalizationRule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="normalization-rule-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div></div>
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <div class="file-field input-field">
            <div class="btn">
                <span>File</span>
                <?= $form->field($model, 'normalizationRuleFile')->fileInput() ?>
            </div>
            <div class="file-path-wrapper">
                <input class="file-path validate" type="text" placeholder="Upload normalization rule file">
            </div>
        </div>
    </div>

    <div class="row">
        <?= $form->field($model, 'state')->checkbox() ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'description')->textarea() ?>

        <p>*Description field supports Markdown syntax (<a href="https://www.markdownguide.org/">Markdown Guide</a>).</p>
    </div>

    <div class="row"></div>


    <div class="row">
        <div class="form-group">
            <?= Html::submitButton('Create' , ['class' => 'waves-effect waves-light green btn',
                'data' => [
                    'method' => 'post',
                ],
                ]) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>
    
</div>
