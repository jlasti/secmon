<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="view-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'refresh_time')->textInput(['placeholder' => 'nY/nM/nW/nD/nH/nm/nS']) ?>

    <?= $form->field($model, 'user_id')->hiddenInput(['value' => Yii::$app->user->getId()])->label(false) ?>

    <?= $form->field($model, 'active')->hiddenInput(['value' => 1])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
