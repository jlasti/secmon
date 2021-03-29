<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\EventsNormalizedSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="events-clustered-runs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'datetime') ?>

    <?= $form->field($model, 'type_of_algoritmus') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'waves-effect waves-light btn']) ?>
        <?= Html::resetButton('Reset', ['class' => 'waves-effect waves-light red btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
