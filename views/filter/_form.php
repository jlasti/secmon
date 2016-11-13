<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Filter */
/* @var $form yii\widgets\ActiveForm */
/* @var $rules app\models\FilterRule */

$this->registerJsFile('@web/js/filter_form.js', ['depends' => 'yii\web\YiiAsset']);
?>

<div class="filter-form">

    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<div id="rules">
		<?php
		foreach($rules as $i => $rule)
		{
			echo $this->render('_ruleForm', ['form' => $form, 'rule' => $rule, 'index' => $i]);
		}
		?>
	</div>

	<div class="form-group">
		<button id="new-rule" type="button">Pridať pravidlo</button>
	</div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
