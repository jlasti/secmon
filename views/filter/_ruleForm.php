<?php
use \app\components\filter;
use \macgyer\yii2materializecss\widgets\form\DatePicker;

$selectedType = $rule->type ?? 'date';
$hideLogic = '';
if ($rulesCount == $index + 1)
    $hideLogic = ' hide';
?>

<div class="rule" data-rule="<?= $index ?>">
    <div class="row">
	    <?= $form->field($rule, "[$index]type", [ 'options' => [ 'class' => 'input-field col m4' ]])->dropDownList($typesDown, [ 'data-rule-type' => $index ]) ?>

        <?php
        foreach ($types as $typeName => $type)
        {
            $additionalClass = '';
            $opt = [];
            if ($typeName != $selectedType)
            {
                $opt['disabled'] = 'disabled';
                $additionalClass = ' hide';
            }

            $r = $type['rule'];
            echo $form->field($rule, "[$index]operator", [
                'options' => [ 'class' => sprintf('input-field col m2%s', $additionalClass), 'data-type' => $typeName ]
            ])->dropDownList($r->getOperatorsForDropdown(), $opt);

            if ($r->getValueType() == filter\FilterValueTypeEnum::DATE)
                echo $form->field($rule, "[$index]value", [
                    'options' => [ 'class' => sprintf('input-field col m5%s',  $additionalClass), 'data-type' => $typeName ]
                ])->textInput(array_merge($opt, [ 'class' => 'datepicker' ]));
            else
                echo $form->field($rule, "[$index]value", [
                    'options' => [ 'class' => sprintf('input-field col m5%s',  $additionalClass), 'data-type' => $typeName ]
                ])->textInput($opt);
        }
        ?>

        <div class="input-field col m1">
            <a href="#" class="btn-floating waves-effect waves-light red" data-rule-remove="<?= $rule->id ?? -1 ?>"
               data-rule-index="<?= $index ?>" data-filter-id="<?= $model->id ?>" onclick="removeRule(this);"><i class="material-icons">remove</i></a>
        </div>
    </div>

    <div class="row<?= $hideLogic ?>" id="logic">
        <?php
            $index += 1;

            echo $form->field($rule, "[$index]logic_operator", [
                'options' => [ 'class' => 'input-field col m2', 'data-type' => 'global' ]
            ])->dropDownList($logicOperators);
        ?>
    </div>
</div>