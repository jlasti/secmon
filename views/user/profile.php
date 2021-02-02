<?php

use app\models\Role;
use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\form\ActiveForm;

$this->params['title'] = 'Change password';

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <?= $form->field($model, 'passwordTextOld')->passwordInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'passwordTextNew')->passwordInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'passwordTextNew2')->passwordInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'waves-effect waves-light btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>