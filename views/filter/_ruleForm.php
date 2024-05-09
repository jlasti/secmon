<?php
use \app\components\filter\FilterTypeEnum;
use bs\Flatpickr\FlatpickrWidget;
use kartik\select2\Select2;
use yii\jui\AutoComplete;


$selectedType = $rule->type ?? 'date';
$hideLogic = '';
$options = ['data-value-type' => 'filter_rule_logic_operator'];
//if ($index == 0)
{
   $hideLogic = ' hide';
    $options['disabled'] = 'disabled';
}


?>



<div class="rule" data-rule="<?= $index ?>">
    <div class="row<?= $hideLogic ?>" id="logic">
        <?php
            echo $form->field($rule, "[$index]logic_operator", [
                'options' => ['class' => 'input-field col m2','id' => 'selectColumnDropdown2', 'data-type' => 'global']
            ])->dropDownList($logicalOperatorsDown, $options);
        ?>
    </div>   

    <div class="row">   

     
        <?= $form->field($rule, "[$index]column", 
                         [ 'options' => [ 'class' => 'input-field col m3', 'id' => 'selectColumnDropdown1']])
            ->dropDownList($colsDown, array_merge($colsDownOptions, [ 'data-rule-column' => $index ])) ?> 

        <?= $form->field($rule, "[$index]id", 
                         ['options' => [ 'class' => 'hide' ]])
            ->hiddenInput([ 'data-value-type' => 'filter_rule_id' ]) ?>
        

        <?= $form->field($rule, "[$index]type", 
                         [ 'options' => [ 'class' => 'input-field col m3', 'id' => 'selectColumnDropdown' ]])
            ->dropDownList($typesDown, [ 'data-rule-type' => $index]) ?>    
        

        <?php
        foreach ($types as $type)
        {
            $typeValue = $type->type();

            echo $form->field($rule, "[$index]operator", [
                'options' => [ 'class' => 'input-field col m2', 'data-type' => $typeValue]
            ])->dropDownList($type->getOperatorsForDropdown(), [ 'data-rule-operator' => $index ]);

            if ($typeValue == FilterTypeEnum::DATE)
            {
                echo $form->field($rule, "[$index]value", [
                    'options' => ['class' => 'input-field col m3', 'data-type' => $typeValue]
                ])->textInput(['class' => 'flatpickr']);
            }
            else
            {
                echo $form->field($rule, "[$index]value", [
                    'options' => ['class' => 'input-field col m3', 'data-type' => $typeValue]
                ])->textInput();
            }
        }
        ?>

        <div class="input-field col m1">
            <a href="#" class="btn-floating waves-effect waves-light red" data-rule-remove="<?= $rule->id ?? -1 ?>"
               data-rule-index="<?= $index ?>" data-filter-id="<?= $model->id ?>" onclick="removeRule(this);"><i class="material-icons">remove</i></a>
        </div>
    </div>
</div>


    





<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<script src="//rawgithub.com/indrimuska/jquery-editable-select/master/dist/jquery-editable-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<link href="//rawgithub.com/indrimuska/jquery-editable-select/master/dist/jquery-editable-select.min.css" rel="stylesheet">

<script>
    // Make select columnn dropdown editable
    $('#selectColumnDropdown1').editableSelect();       
</script>
