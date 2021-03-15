<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\EventsNormalizedSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="events-normalized-search">

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

    <?= $form->field($model, 'src_ip') ?>

    <?= $form->field($model, 'dst_ip') ?>

    <?= $form->field($model, 'src_port') ?>

    <?= $form->field($model, 'dst_port') ?>

    <?= $form->field($model, 'protocol') ?>

    <?= $form->field($model, 'src_mac') ?>

    <?= $form->field($model, 'dst_mac') ?>

    <?= $form->field($model, 'request_url') ?>

    <?= $form->field($model, 'request_client_application') ?>

    <?= $form->field($model, 'extensions') ?>

    <?= $form->field($model, 'raw') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'waves-effect waves-light btn']) ?>
        <?= Html::resetButton('Reset', ['class' => 'waves-effect waves-light red btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
