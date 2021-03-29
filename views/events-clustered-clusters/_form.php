<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\EventsClusteredClusters */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="events-clustered-clusters-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'severity')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'comment')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
