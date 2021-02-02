<?php

use app\models\Role;
use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

$roles = Role::find()->indexBy('id')->all();

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'passwordText')->passwordInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="row">
        <input type="hidden" name="User[rolesList]" value />
        <div class="switch">
        <?php foreach ($roles as $role) : ?>
            <label>
                <strong><?= $role->name; ?></strong>
                <input type="checkbox" name="User[rolesList][]" value="<?= $role->id ?>" <?=
                    array_key_exists($role->slug, $model->roles) ? "checked='checked'" : "" ?>>
                <span class="lever"></span>
            </label>
        <?php endforeach; ?>
        </div>
    </div>

    <div class="row">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'waves-effect waves-light green btn' : 'waves-effect waves-light btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
