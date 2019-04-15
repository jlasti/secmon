<?php
use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Filter */
/* @var $form yii\widgets\ActiveForm */
/* @var $rules app\models\FilterRule */

$this->registerJsFile('@web/js/filter_form.js', ['depends' => 'yii\web\YiiAsset']);

$colsDown = \app\models\EventsNormalized::getColumnsDropdown();
$colsDownOptions = \app\models\EventsNormalized::getColumnsDropdownOptions();
$types = \app\models\FilterRule::types();
$typesDown = \app\models\FilterRule::getTypesForDropdown();
$logicalOperatorsDown = \app\models\FilterRule::getLogicalOperatorsForDropdown();
?>

<div class="filter-form">
    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<div id="rules">
		<?php
		foreach($rules as $index => $rule)
			echo $this->render('_ruleForm', [
			    'form' => $form,
                'rule' => $rule,
                'index' => $index,
                'model' => $model,
                'types' => $types,
                'colsDown' => $colsDown,
                'colsDownOptions' => $colsDownOptions,
                'typesDown' => $typesDown,
                'logicalOperatorsDown' => $logicalOperatorsDown
            ]);
		?>
	</div>

	<div class="form-group">
		<button id="new-rule" type="button" class='btn-floating waves-effect waves-light btn-large red'><i class='material-icons'>add</i></button>
	</div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'waves-effect waves-light green btn input-field' : 'waves-effect waves-light btn input-field']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
