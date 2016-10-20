<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Event\EventType;

/* @var $this yii\web\View */
/* @var $model app\models\Event\Event */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'timestamp')->textInput() ?>

    <?= $form->field($model, 'type_id')->dropDownList(EventType::getAllAsArray()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
