<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SecRule\SecRuleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sec-rule-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'link') ?>
    
    <?= $form->field($model, 'state') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'waves-effect waves-light btn']) ?>
        <?= Html::resetButton('Reset', ['class' => 'waves-effect waves-light red btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
