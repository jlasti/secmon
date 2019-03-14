<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\EventsCorrelatedSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="events-correlated-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'datetime') ?>

    <?= $form->field($model, 'host') ?>

    <?= $form->field($model, 'cef_version') ?>

    <?= $form->field($model, 'cef_vendor') ?>

    <?= $form->field($model, 'cef_dev_prod') ?>

    <?= $form->field($model, 'cef_dev_version') ?>

    <?= $form->field($model, 'cef_event_class_id') ?>

    <?= $form->field($model, 'cef_name') ?>

    <?= $form->field($model, 'cef_severity') ?>

    <?= $form->field($model, 'parent_events') ?>

    <?= $form->field($model, 'raw') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'waves-effect waves-light btn']) ?>
        <?= Html::resetButton('Reset', ['class' => 'waves-effect waves-light red btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
