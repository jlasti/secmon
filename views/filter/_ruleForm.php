<?php
$operators = \app\components\filter\DateFilterRule::operators();
?>

<div class="rule row">

	<?= $form->field($rule, "[$index]type", [ 'options' => [ 'class' => 'input-field col s10 m5 l5' ]])->dropDownList(\app\models\FilterRule::types()) ?>

	<?= $form->field($rule, "[$index]operator", [ 'options' => [ 'class' => 'input-field col s2 m2 l1' ]])->dropDownList(array_combine($operators, $operators)) ?>

	<?= $form->field($rule, "[$index]value", [ 'options' => [ 'class' => 'input-field col s12 m5 l6' ]])->textInput() ?>

</div>