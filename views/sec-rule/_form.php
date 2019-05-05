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
        <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
     <div class="file-field input-field">
        <div class="btn">
         <span>File</span>
            <?= $form->field($model, 'secConfigFile')->fileInput() ?>
        </div>
        <div class="file-path-wrapper">
            <input class="file-path validate" type="text" placeholder="Upload sec rule file">
        </div>
     </div>
    </div>

    <div class="row">
        <?= $form->field($model, 'state')->checkbox() ?>
    </div>

    <div class="row"></div>
			
    <div class="row">
        <div class="form-group">
            <?= Html::submitButton('Create' , ['class' => 'waves-effect waves-light green btn',
                'data' => [
                    'confirm' => Yii::t('app', "To start using added active rule, systemctl restart secmon-corellator.service is required !!"),
                    'method' => 'post',
                ], ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
