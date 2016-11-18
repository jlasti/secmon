<?php
use \app\components\filter;
use \macgyer\yii2materializecss\widgets\form\DatePicker;

$selectedType = $rule->type ?? 'date';
?>

<div class="rule row" data-rule="<?= $index ?>">

	<?= $form->field($rule, "[$index]type", [ 'options' => [ 'class' => 'input-field col m4' ]])->dropDownList($typesDown, [ 'data-rule-type' => $index ]) ?>

	<?php
	$r = null;
	foreach ($types as $i => $type)
	{
		$additionalClass = '';
		$opt = [];
		if ($i != $selectedType)
		{
			$opt['disabled'] = 'disabled';
			$additionalClass = ' hide';
		}

		$r = $type['rule'];
		echo $form->field($rule, "[$index]operator", [
			'options' => [ 'class' => sprintf('input-field col m2%s', $additionalClass), 'data-type' => $i ]
		])->dropDownList($r->getOperatorsForDropdown(), $opt);


		if ($r->getValueType() == filter\FilterValueTypeEnum::Date)
			echo $form->field($rule, "[$index]value", [
			'options' => [ 'class' => sprintf('input-field col m5%s',  $additionalClass), 'data-type' => $i ]
				])->textInput(array_merge($opt, [ 'class' => 'datepicker' ]));
		else
			echo $form->field($rule, "[$index]value", [
				'options' => [ 'class' => sprintf('input-field col m5%s',  $additionalClass), 'data-type' => $i ]
			])->textInput($opt);
	}
	?>

	<div class="input-field col m1">
		<a href="#" class="btn-floating waves-effect waves-light red" data-rule-remove="<?= $rule->id ?? -1 ?>" data-rule-index="<?= $index ?>" data-filter-id="<?= $model->id ?>" onclick="removeRule(this);"><i class="material-icons">remove</i></a>
	</div>

</div>