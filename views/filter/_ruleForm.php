<?php
$operators = \app\components\filter\DateFilterRule::operators();
?>

<div class="rule">
	<?= $form->field($rule, "[$index]type")->dropDownList(\app\models\FilterRule::types()) ?>

	<?= $form->field($rule, "[$index]operator")->dropDownList(array_combine($operators, $operators)) ?>

	<?= $form->field($rule, "[$index]value")->textInput() ?>
</div>