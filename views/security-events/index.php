<?php

use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\cmenu\ContextMenu;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\SecurityEventsPage;
use app\models\Filter;
use app\models\FilterRule;
use \app\models\SecurityEvents;
use \app\controllers\FilterController;
use \app\controllers\SecurityEventsController;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SecurityEventsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Security Events';
$loggedUserId = Yii::$app->user->getId();
$securityEventsPage = SecurityEventsPage::findOne(['user_id' => $loggedUserId]);
$rawDataColumns = explode(",", $securityEventsPage->data_columns);
$dataColumns = SecurityEventsPage::replaceColumns($rawDataColumns, $searchModel);
$filters = FilterController::getFiltersOfUser($loggedUserId);
$selectedFilterId = SecurityEventsPage::findOne(['user_id' => $loggedUserId])->getAttribute('filter_id');
$timeFilterId = SecurityEventsPage::findOne(['user_id' => $loggedUserId])->getAttribute('time_filter_id');
$selectedFilter = Filter::findOne(['id' => $selectedFilterId]);
$timeFilter = Filter::findOne(['id' => $timeFilterId]);
$filter = new Filter();
$colsDown = SecurityEvents::getColumnsDropdown();
$columns = explode(",", $securityEventsPage->data_columns);

$relativeTimeFilter = '';
$absoluteTimeFilter = (object) [
    'from' => '',
    'to' => '',
  ];
 

if($securityEventsPage->time_filter_type == 'relative')
{
    $relativeTimeFilter = FilterController::getRelativeTimeFilterValue();
}

if($securityEventsPage->time_filter_type == 'absolute' && $securityEventsPage->time_filter_id)
{
    $absoluteTimeFilter = FilterController::getAbsoluteTimeFilterValue();
}

// If auto_refresh is set to true, then set interval for content update
if($securityEventsPage->auto_refresh)
{
    $this->registerJs('
    var refreshString = "' . $securityEventsPage->refresh_time .'" ;
    
    function getRefreshTime(refreshString) {
        if (refreshString == "0") {
            return 0;
        }
        var timeUnit = refreshString.substring(
        refreshString.length - 1,
        refreshString.length
        );
        var refreshTime = parseInt(
        refreshString.substring(0, refreshString.length - 1)
        );
        if (timeUnit == "S") {
            return refreshTime;
        }
        refreshTime *= 60;
        if (timeUnit == "m") {
            return refreshTime;
        }
        refreshTime *= 60;
        if (timeUnit == "H") {
            return refreshTime;
        }
        return refreshTime * 24;
        if (timeUnit == "D") {
            return refreshTime;
        }
        if (timeUnit == "W") {
            return refreshTime * 7;
        }
        if (timeUnit == "M") {
            return refreshTime * 30;
        }
        if (timeUnit == "Y") {
            return refreshTime * 365;
        }
    }

    setInterval(function() {
        $.pjax.reload({
            container:"#pjaxContainer table#eventsContent tbody:last", 
            fragment:"table#eventsContent tbody:last"})
            .done(function() {
                activateEventsRows();
                $.pjax.reload({
                    container:"#pjaxContainer #pagination", 
                    fragment:"#pagination"
                });
            });
        }, getRefreshTime(refreshString)*1000 );
    ');
}
?>

<div class="security-events-page-panel" style="padding-top: 0;">
    <div class="row">
        <div class="col" style="width:30%;">
            <label class="active" for="name">Selected Filter</label>
            <?= Html::beginForm(['apply-selected-filter'],'post'); ?>
                <?= Html::activeDropDownList($filter, 'name', ArrayHelper::map($filters,'name','name'), ['value' => !empty($selectedFilter) ? $selectedFilter->name : '', 'prompt' => 'None', 'style' => !empty($selectedFilter) ? 'color: black;' : 'color: gray;', 'id' => 'eventFilterSelect', 'onchange' => 'this.form.submit()']); ?>
            <?= Html::endForm(); ?>
            <?= Html::a("<i class='material-icons'>add</i>", ['filter/create', 'securityEventsPage' => true], ['class' => 'btn btn-success', 'title' => 'Create new filter']) ?>
            <?= Html::a("<i class='material-icons'>edit</i>", ['filter/update', 'id' => $selectedFilterId, 'securityEventsPage' => true], ['class' => 'btn btn-success', 'title' => 'Edit selected filter', 'disabled' => !empty($selectedFilter) ? false : true ]); ?>
            <?= Html::a("<i class='material-icons'>delete</i>", ['remove-selected-filter'], ['class' => 'btn btn-danger', 'style' => 'background-color: red;', 'title' => 'Remove selected filter', 'disabled' => !empty($selectedFilter) ? false : true ]) ?>
            <div <?= $selectedFilterId ? 'class="filter-rule"' : ''?>>
                <p>
                    <?php
                        if($selectedFilter)
                        {
                            $rules = FilterRule::find()->where(['filter_id' => $selectedFilterId])->orderBy(['position' => SORT_ASC])->all();
                            foreach($rules as $idx => $rule)
                            {
                                if($idx == 0)
                                    echo $rule->column . " " . $rule->operator . " " . $rule->value . " ";
                                else
                                    echo $rule->logic_operator . " " . $rule->column . " " . $rule->operator . " " . $rule->value . " ";
                            }
                        }
                    ?>
                </p>
            </div>    
        </div>

        <div class="col" style="width:40%;">
            <label class="active" for="name">Time Filter</label>
            <?= Html::beginForm(['update-time-filter'],'post'); ?>
                <div id="absoluteTimeForm" style="display:none;">
                    <div class="col" style="width:50%; padding-left: 0;">
                        <?php
                            echo DateTimePicker::widget([
                                'name' => 'absoluteTimeFrom',
                                'options' => ['placeholder' => 'From'],
                                'value' => $absoluteTimeFilter->from,
                                'type' => DateTimePicker::TYPE_INPUT,
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-mm-dd hh:ii',
                                    'todayHighlight' => true
                                ]
                            ]);
                        ?>
                    </div>
                    <div class="col" style="width:50%; padding-right: 0;">
                        <?php
                            echo DateTimePicker::widget([
                                'name' => 'absoluteTimeTo',
                                'options' => ['placeholder' => 'To'],
                                'value' => $absoluteTimeFilter->to,
                                'type' => DateTimePicker::TYPE_INPUT,
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd hh:ii',
                                    'todayHighlight' => true
                                ]
                            ]);
                        ?>
                    </div>
                </div>
                <div id="relativeTimeForm" style="display:none; padding-right: 20px">
                    <select id="relativeTimeFormSelect" name="relativeTime" placeholder="nY/nM/nW/nD/nH/nm/nS" value="<?= $relativeTimeFilter ?>">
                        <option value="10m">10m</option>
                        <option value="30m">30m</option>
                        <option value="1H">1H</option>
                        <option value="24H">24H</option>
                        <option value="7D">7D</option>
                    </select>
                </div>
                
                <div class="col" style="width:50%;">
                    <input class="form-check-input" type="radio" name="timeFilterType" id="inlineRadioAbsolute" value="absolute" onclick="showAbsoluteTimeForm()" <?= $securityEventsPage->time_filter_type == 'absolute' ? 'checked=""' : '' ?> >
                    <label class="form-check-label" for="inlineRadioAbsolute">Absolute</label>
                    <input class="form-check-input" type="radio" name="timeFilterType" id="inlineRadioRelative" value="relative" onclick="showRelativeTimeForm()" <?= $securityEventsPage->time_filter_type == 'relative' ? 'checked=""' : '' ?> >
                    <label class="form-check-label" for="inlineRadioRelative">Relative</label>
                </div>

                <div class="col" style="width:50%; padding: 0;">
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-success', 'title' => 'Update page refresh time', 'style' => 'float: right; margin-right: 0;']) ?>
                    </div>
                 </div>
            <?= Html::endForm(); ?>
        </div>

        <div class="col">
            <?php $form = ActiveForm::begin(['action' =>['update-refresh-time'], 'method' => 'post',]); ?>
                <?= $form->field($securityEventsPage, 'refresh_time')->textInput(['placeholder' => 'nY/nM/nW/nD/nH/nm/nS']) ?>
                <div class="form-group">
                        <?= Html::a("<i class='material-icons'>refresh</i>", ['index'], ['class' => 'btn btn-success', 'title' => 'Refresh page']) ?>
                        <?= Html::a($securityEventsPage->auto_refresh ? "<i class='material-icons'>pause</i>" : "<i class='material-icons'>play_arrow</i>",
                            ['start-pause-auto-refresh'],
                            [
                                'class' => 'btn btn-success',
                                'title' => $securityEventsPage->auto_refresh ? 'Pause auto refresh' : 'Resume auto refresh'
                            ]
                        )?>
                        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-success', 'title' => 'Update page refresh time']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="security-events-index clickable-table">
    <?php Pjax::begin(['id' => 'pjaxContainer']); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'layout' => '{items}<div id="pagination">{pager}</div>',
                'tableOptions' => [
                    'id' => 'eventsContent',
                    'class' => 'responsive-table striped'
                ],
                'columns' => $dataColumns,
            ]); ?>
    <?php Pjax::end(); ?>
</div>

<a href="#modalColumsSettings" class="btn-floating waves-effect waves-light btn-small blue"
    style="position:absolute; top: 30px; right: 40px; display: 'block' ?>" data-toggle="tooltip" data-placement="bottom" title="Columns settings">
    <i class="material-icons">settings</i>
</a>

<!-- Modal Structure -->
<div class="modal" id="modalColumsSettings">
    <div class="modal-content">
    <h4>Table columns</h4>
        <form action="#">
            <div class="row">
                <div class="input-field col s11">
                    <div class="chips chips-table" id="chipstable">
                        <?php foreach ($columns as $column ) : ?>
                            <div class="chip" value="<?= $column ?>">
                                <?= $column ?>
                                <i class="close material-icons">close</i>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="input-field col s11">
                    <label class="active" for="name">Add Column</label>
                    <select class="form-select" id="selectColumnDropdown" aria-label="Default select example">
                    <?php foreach ($colsDown as $key => $value) : ?>
                        <option value="<?= $key ?>"><?= $value ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-field col s1">
                    <div class="help-block left-align">
                        <a href="#" id="addColumn" class="btn-floating btn-small waves-effect waves-light red"
                            title="Add new column">
                            <i class="material-icons">add</i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
    <div class="right">
        <button id="saveSelectedColumns" class="modal-action modal-close waves-effect waves-green btn-flat">Save</button>
        <button class=" modal-close waves-effect waves-green btn-flat">Cancel</button>
    </div>
    </div>    
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<script src="//rawgithub.com/indrimuska/jquery-editable-select/master/dist/jquery-editable-select.min.js"></script>
<link href="//rawgithub.com/indrimuska/jquery-editable-select/master/dist/jquery-editable-select.min.css" rel="stylesheet">

<script>
    // Check which radio button is checked
    if($('input[name="timeFilterType"]:checked').val() == 'absolute')
        showAbsoluteTimeForm();
    
    if($('input[name="timeFilterType"]:checked').val() == 'relative'){
        showRelativeTimeForm();
    }

    // Create relative time form editable
    $('#relativeTimeFormSelect').editableSelect();

    // Create sortable chips for security events table
    var $sortableChips = $( "#chipstable" );
    $sortableChips.sortable();

    var $sortable = $( "#eventsContent > thead > tr" );
    $sortable.sortable({
        stop: function (event, ui) {
            const eventTable = document.getElementById("eventsContent");
            const thElements = eventTable.getElementsByTagName('chip > a');
            const columnsList = document.querySelectorAll('[data-sort]');
            var selectedColumns = [];
            
            for (let i = 0; i < columnsList.length; i++) {
                selectedColumns.push(columnsList[i].getAttribute('data-sort').replace('-', ''))
            }

            $.post("/secmon/web/security-events/update-selected-columns", {value:selectedColumns});
        }
    });
    
    // Create new column chip based on selected chip from dropdown list
    $("#addColumn").on("click", function (event) {
        // Get selected Column value
        var element = document.getElementById("selectColumnDropdown");
        var value = element.value;

        if(checkColumnExistence(value)){
            Materialize.toast(
              'Column "' + value + '" already in list!',
              2000
            );
            return;
        }

        // Create a "div" node for column chip:
        const chipNode = document.createElement("div");
        chipNode.className = "chip ui-sortable-handle";
        chipNode.setAttribute('value', value);

        // Create a text node for column chip:
        const chipText = document.createTextNode(value);
        
        // Create an "icon" node:
        const icon = document.createElement("i");
        const iconText = document.createTextNode('close');
        icon.className = "close material-icons";
        icon.appendChild(iconText);

        // Append the text node and icon to the "div" node:
        chipNode.appendChild(chipText);
        chipNode.appendChild(icon);

        // Append the "div" node to the list of chips:
        document.getElementById("chipstable").appendChild(chipNode);
    });

    // Save selected columns into database
    $("#saveSelectedColumns").on("click", function (event, ui) {
        var selectedColumns = extractColumnsFromChips()
        $.post("/secmon/web/security-events/update-selected-columns", {value:selectedColumns});
    });

    //bude treba dat event na spustenie aj pri prekliknuti page

    const tableBody = document.getElementsByTagName("tbody")[0];
    const columnsList = document.querySelectorAll('[data-sort]');
    const tableRowsLength = tableBody.getElementsByTagName("tr").length;
    var tableCells = tableBody.getElementsByTagName("td");

    // Create hidden element for add attribute to event filter
    // TODO co ked je filter event prazdny??
    addHoverElementOnTableCells(tableCells[4], 4);



    
    //tableCells[0].innerHTML = "Milk";
    // for each cyklus, s tym ze vzdy musi vynechat cisla

    //Jquery hover on element with id

    // Add Hover Elements on each cell in table
    function addHoverElementOnTableCells(cell, id) {
        var div = document.createElement("div");
        //div.classList.add("table-cell-window");
        div.setAttribute("id", "table-cell-" + id);

        // Create Header element
        var header = document.createElement("p");
        header.appendChild(document.createTextNode("Add to filter: "));

        // Create Link elements
        var andIsLink = document.createElement("a");
        var andIsNotLink = document.createElement("a");
        var orIsLink = document.createElement("a");

        // Post action
        andIsLink.setAttribute("href", "#");

        andIsLink.appendChild(document.createTextNode("AND is " + cell.textContent));
        andIsNotLink.appendChild(document.createTextNode("AND is not " + cell.textContent));
        orIsLink.appendChild(document.createTextNode("OR is " + cell.textContent));

        div.appendChild(header);
        div.appendChild(andIsLink);
        div.appendChild(document.createElement("br"));
        div.appendChild(andIsNotLink);
        div.appendChild(document.createElement("br"));
        div.appendChild(orIsLink);

        cell.appendChild(div);

    }

    function extractColumnsFromChips() {
        const chipsTable = document.getElementById("chipstable");
        const elements = chipsTable.getElementsByClassName('chip');
        var chips = Array.prototype.slice.call( elements );
        var selectedColumns = [];
        chips.forEach(chip => selectedColumns.push(chip.getAttribute('value')));
        return selectedColumns;
    }

    function checkColumnExistence(newColumn) {
        var selectedColumns = extractColumnsFromChips();
        return selectedColumns.includes(newColumn);
    }

    function showAbsoluteTimeForm() {
        $("#absoluteTimeForm").show();
        $("#relativeTimeForm").hide();
    }

    function showRelativeTimeForm() {
        $("#absoluteTimeForm").hide();
        $("#relativeTimeForm").show();
    }
</script>