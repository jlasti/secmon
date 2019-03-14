<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\EventsCorrelated */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="events-correlated-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'datetime')->textInput() ?>

    <?= $form->field($model, 'host')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cef_version')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cef_vendor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cef_dev_prod')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cef_dev_version')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cef_event_class_id')->textInput() ?>

    <?= $form->field($model, 'cef_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cef_severity')->textInput() ?>

    <?= $form->field($model, 'parent_events')->textInput() ?>

    <?= $form->field($model, 'raw')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
