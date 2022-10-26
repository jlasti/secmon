<?php

use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\cmenu\ContextMenu;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use app\components\filter\FilterQuery;
use app\models\SecurityEventsPage;
use app\models\Filter;
use app\models\FilterRule;
use \app\models\SecurityEvents;
use \app\controllers\FilterController;
use \app\controllers\SecurityEventsController;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SecurityEventsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $chartDataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Security Events';
$loggedUserId = Yii::$app->user->getId();
$securityEventsPage = SecurityEventsPage::findOne(['user_id' => $loggedUserId]);
$rawDataColumns = explode(",", $securityEventsPage->data_columns);
$dataColumns = SecurityEventsController::replaceColumns($rawDataColumns, $searchModel);
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
?>

<div class="security-events-page-panel">
    <div class="row" style="margin-bottom: 0;">
        <div class="col" style="width:30%;">
            <div class="row security-panel-header">
                <div class="col" style="float: left;">
                    <span class="label">Selected filter:</span>
                </div>
                <div class="col" style="float:right;">
                    <?= Html::a("<span><i class='material-icons'>delete</i></span>", ['delete-selected-filter'],
                        [
                            'class' => 'btn btn-danger',
                            'style' => 'background-color: red;',
                            'title' => 'Remove selected filter',
                            'disabled' => !empty($selectedFilter) ? false : true,
                            'data' => [
                                'confirm' => Yii::t('app', 'Selected filter will be permanently deleted. Are you sure you want to delete this filter?'),
                                'method' => 'post',
                            ],
                        ])
                    ?>
                </div>
                <div class="col" style="float:right;">
                    <?= Html::a("<span><i class='material-icons'>clear</i></span>", ['remove-selected-filter'], ['class' => 'btn btn-danger', 'style' => 'background-color: orange;', 'title' => 'Clear selected filter', 'disabled' => !empty($selectedFilter) ? false : true ]) ?>
                </div>
                <div class="col" style="float:right;">
                    <?= Html::a("<span><i class='material-icons'>edit</i></span>", ['filter/update', 'id' => $selectedFilterId, 'securityEventsPage' => true], ['class' => 'btn btn-success', 'title' => 'Edit selected filter', 'disabled' => !empty($selectedFilter) ? false : true ]); ?>
                </div>
                <div class="col" style="float:right;">
                    <?= Html::a("<span><i class='material-icons'>add</i></span>", ['filter/create', 'securityEventsPage' => true], ['class' => 'btn btn-success', 'title' => 'Create new filter']) ?>
                </div>
            </div>
            <?= Html::beginForm(['apply-selected-filter'],'post', ['style' => 'padding-right: 20px;']); ?>
                <?= Html::activeDropDownList($filter, 'name', ArrayHelper::map($filters,'name','name'), ['value' => !empty($selectedFilter) ? $selectedFilter->name : '', 'style' => !empty($selectedFilter) ? 'color: black;' : 'color: gray;', 'id' => 'eventFilterSelect', 'onchange' => 'this.form.submit()']); ?>
            <?= Html::endForm(); ?>
        </div>

        <div class="col" style="width:40%;">
            <?= Html::beginForm(['update-time-filter'],'post'); ?>    
                <div class="row security-panel-header" style="margin-bottom: 5px;">
                    <div class="col" style="float: left;">
                        <span class="label">Time filter:</span>
                    </div>
                    <div class="col" style="float: right; padding: 0;">
                        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-success', 'title' => 'Update page refresh time']) ?>
                    </div>
                    <div class="col" style="float: right; padding-right: 20px;">
                        <input class="form-check-input" type="radio" name="timeFilterType" id="inlineRadioAbsolute" value="absolute" onclick="showAbsoluteTimeForm()" <?= $securityEventsPage->time_filter_type == 'absolute' ? 'checked=""' : '' ?> >
                        <label class="form-check-label" for="inlineRadioAbsolute">Absolute</label>
                        <input class="form-check-input" type="radio" name="timeFilterType" id="inlineRadioRelative" value="relative" onclick="showRelativeTimeForm()" <?= $securityEventsPage->time_filter_type == 'relative' ? 'checked=""' : '' ?> >
                        <label class="form-check-label" for="inlineRadioRelative">Relative</label>
                    </div>
                </div>
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
                <div id="relativeTimeForm" style="display:none; width:50%; float: lefft; padding-right: 20px;">
                    <select id="relativeTimeFormSelect" name="relativeTime" placeholder="nY/nM/nW/nD/nH/nm/nS" value="<?= $relativeTimeFilter ?>">
                        <option value="10m">10m</option>
                        <option value="30m">30m</option>
                        <option value="1H">1H</option>
                        <option value="24H">24H</option>
                        <option value="7D">7D</option>
                    </select>
                </div>
            <?= Html::endForm(); ?>
        </div>

        <div class="col" style="width:30%;"">
            <div class="row security-panel-header">
                <?php $form = ActiveForm::begin(['action' =>['update-refresh-time'], 'method' => 'post',]); ?>
                    <div class="col" style="float: left;">
                        <span class="label">Refresh time</span>
                    </div>
                    <div class="col" style="float:right;">
                        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-success', 'title' => 'Update page refresh time']) ?>
                    </div>
                    <div class="col" style="float:right;">
                        <?= Html::button("<i class='material-icons'>play_arrow</i>",
                            [
                                'class' => 'btn btn-success',
                                'id' => 'autoRefreshButton',
                                'value' => 'start',
                                'title' => 'Start auto refresh'
                            ])
                        ?>          
                    </div>
                    <div class="col" style="float:right;">
                        <?= Html::button("<i class='material-icons'>refresh</i>",
                            [
                                'class' => 'btn btn-success',
                                'title' => 'Refresh page',
                                'onclick' => 'location.reload()'
                            ])
                        ?>
                    </div>
                </div>
                <?= $form->field($securityEventsPage, 'refresh_time')->textInput(['placeholder' => 'nY/nM/nW/nD/nH/nm/nS'])->label(false) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="row" style="margin-bottom: 0;">
        <div class="col" style="width:100%;">
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
    </div>
</div>

<?php
$chartData = FilterController::getEventsToBarChart($selectedFilterId, $timeFilterId);
$series = [];
$series = [['name' => 'Security Events', 'data' => []]];

foreach($chartData as $key => $record)
{
    $tmpRecord = [$record['time'], $record['count']];
    array_push($series[0]['data'], $tmpRecord);
}
?>

<div class="security-events-index", id="securityEventsBarChart" style="height: 160px;">
    <?php Pjax::begin(['id' => 'pjaxBarChartContainer']); ?>
        <?= \onmotion\apexcharts\ApexchartsWidget::widget([
            'type' => 'bar', // area, scatter
            'height' => '150', // default 350
            'chartOptions' => [
                'chart' => [
                    'toolbar' => [
                        'show' => true,
                        'autoSelected' => 'zoom'
                    ],
                    'events' => [
                        'zoomed' => new JsExpression('function (chartContext, { xaxis }) {
                            var from = moment.unix(xaxis.min/1000).format("YYYY-MM-DD HH:mm");
                            var to = moment.unix(xaxis.max/1000).format("YYYY-MM-DD HH:mm");
                            updateAbsoluteTimeFilter(from, to);
                        }'),
                    ],
                    'animations' => [
                        'enabled' => false,
                    ]
                ],  
                'xaxis' => [
                    'type' => 'datetime',
                    'thickAmount' => 2,
                    'labels' => [
                        'format' => 'd/M HH:MM:ss',
                    ]
                ],
                'yaxis' => [
                    'type' => 'numeric',
                    'min' => 0,
                    'seriesName' => 'Events Count',
                ],
                'tooltip' => [
                    'x' => [
                      'format' => 'MMM dd hh:mm:ss'
                    ]
                ],
                'plotOptions' => [
                    'bar' => [
                        'horizontal' => false,
                    ],
                ],
                'dataLabels' => [
                    'enabled' => false
                ],
                'stroke' => [
                    'show' => true,
                    'colors' => ['transparent']
                ],
                'legend' => [
                    'verticalAlign' => 'bottom',
                    'horizontalAlign' => 'left',
                ],
            ],
            'series' => $series,
        ]);
        ?>      
    <?php Pjax::end(); ?>
</div>

<a href="#modalColumsSettings" class="btn-floating waves-effect waves-light btn-small blue columns-settings-button"
    style="position:absolute; right: 10px; margin-bottom: 20px; display: 'block'; ?>" data-toggle="tooltip" data-placement="bottom" title="Columns settings">
    <i class="material-icons">settings</i>
</a>

<div class="security-events-index clickable-table", id="securityEventsTable">
    <?php Pjax::begin(['id' => 'pjaxContainer']); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{items}<div id="pagination">{pager}</div>',
                'tableOptions' => [
                    'id' => 'eventsContent',
                    'class' => 'responsive-table striped',
                ],
                'columns' => $dataColumns,
            ]); ?>
    <?php Pjax::end(); ?>
</div>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<link href="//rawgithub.com/indrimuska/jquery-editable-select/master/dist/jquery-editable-select.min.css" rel="stylesheet">

<script>
    $(".container").css("padding-top", "10px");

    // Add hover elements on table cells
    addHoverElementOnTableCells();

    // Create sortable chips for security events table
    var $sortableChips = $( "#chipstable" );
    $sortableChips.sortable();

    var $sortable = $( "#eventsContent > thead > tr" );

    var intervalId;
    var refreshString = "<?php echo $securityEventsPage->refresh_time; ?>";
    //startInterval(getRefreshTime(refreshString)*1000);

    // Start refresh interval
    function startInterval(interval) {
        // Store the ID of the interval to variable so we can clear it later
        intervalId = setInterval(function() {
            $.pjax.reload({
                container:"#pjaxContainer table#eventsContent tbody:last", 
                fragment:"table#eventsContent tbody:last"})
                .done(function() {
                    activateEventsRows();
                    $.pjax.reload({
                        container:"#pjaxContainer #pagination", 
                        fragment:"#pagination"
                    });
                    $.pjax.reload({container: "#pjaxBarChartContainer", async:true});
                    addHoverElementOnTableCells();
                });
        }, interval);
    }

    // Handle Pause/Resume auto refresh button
    $('#autoRefreshButton').on('click', function() {
        var autorefresh = $('#autoRefreshButton').attr('value');
        if(autorefresh == 'stop')
        {
            clearInterval(intervalId);
            $('#autoRefreshButton i').text("play_arrow");
            $('#autoRefreshButton').attr('value', 'start');
            $('#autoRefreshButton').attr('title', 'Start auto refresh');
        }  
        else
        {
            startInterval(getRefreshTime(refreshString)*1000);
            $('#autoRefreshButton i').text("pause");
            $('#autoRefreshButton').attr('value', 'stop');
            $('#autoRefreshButton').attr('title', 'Stop auto refresh');
        }
    })

    // Check which radio button is checked
    if($('input[name="timeFilterType"]:checked').val() == 'absolute')
        showAbsoluteTimeForm();
    
    if($('input[name="timeFilterType"]:checked').val() == 'relative'){
        showRelativeTimeForm();
    }

    // Make Relative Time filter select editable
    $('#relativeTimeFormSelect').editableSelect();

    // Make Filter select editable
    $('#eventFilterSelect').editableSelect();

    // Make select columnn dropdown editable
    $('#selectColumnDropdown').editableSelect();

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
        var column = document.getElementById("selectColumnDropdown");
        var colunmValue = column.value;
        var objectColumns = <?php echo json_encode($colsDown); ?>;
        var columnsKeyNames = getColumnsKeyNames();

        if(!validateColumnName(colunmValue, objectColumns)){
            Materialize.toast(
            'Column "' + colunmValue + '" does not exist!',
            2000
            );
            return;
        }

        if(checkColumnExistence(colunmValue, objectColumns)){
            Materialize.toast(
            'Column "' + colunmValue + '" already in list!',
            2000
            );
            return;
        }

        // Create a "div" node for column chip:
        const chipNode = document.createElement("div");
        chipNode.className = "chip ui-sortable-handle";
        newChipName = Object.keys(objectColumns).find(key => objectColumns[key] === colunmValue);
        chipNode.setAttribute('value', newChipName);

        // Create a text node for column chip:
        const chipText = document.createTextNode(newChipName);
        
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

    // Validation of input values which should be added to filter
    $("#securityEventsTable").on("submit", ".table-cell-window form", (function(){
        var form = $(this);
        var id = '#'+form.attr('id');
        var $inputs = $(id+' :input');
        var values = {};
        $inputs.each(function() {
            values[this.name] = $(this).val();
        });

        if(values['value'] == 'undefined' || values['value'] === "(not set)" || values['value'].length === 0){
            Materialize.toast('Selected value "' + values['value'] + '" of column "' + values['column'] + '" can not be added to filter!', 3500);
            return false;
        }
    }));

    // Add Hover Elements on table cells after pagination is used
    $("#securityEventsTable").on("click", function(){
        $(document).on('pjax:end', function() {
            addHoverElementOnTableCells();
        });
    });

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

    function addHoverElementOnTableCells() {
        const tableBody = document.getElementsByTagName("tbody")[0];
        const columnsList = document.querySelectorAll('[data-sort]');
        const tableRowsLength = tableBody.getElementsByTagName("tr").length;
        const rawTableCells = tableBody.getElementsByTagName("td");
        const numberOfCells = rawTableCells.length;
        var tableCells = [];
    
        // Create new array of table cells without cells in last column with detailed view
        for (let index = 0; index < numberOfCells; index++) {
            if((index % (columnsList.length + 2) != 0 && index % (columnsList.length + 2) != columnsList.length + 1 )){
                tableCells.push(rawTableCells[index]); 
            }
        }

        for (let index = 0; index < tableCells.length; ++index) {
            idx = index % columnsList.length;
            column = columnsList[idx].getAttribute('data-sort').replace('-', '');
            addHoverElementOnTableCell(tableCells[index], index, column);
        }
    }

    function addHoverElementOnTableCell(cell, index, column) {
        if(cell.firstChild )
            cellContent = cell.firstChild.data;
        else
            cellContent = cell.contentText;

        $('<div class="table-cell-window">\
            <p>Add to filter:</p>\
            <form id="addAttributeToFilterForm-1-' + index + '" action="/secmon/web/security-events/add-attribute-to-filter" method="post">\
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />\
            <input type="hidden" name="operator" value="AND">\
            <input type="hidden" name="negation" value=false>\
            <input type="hidden" name="value" value="' + cellContent + '">\
            <input type="hidden" name="column" value="' + column + '">\
            <input type="submit" value="+ AND is ' + cellContent + '">\
            </form>\
            <form id="addAttributeToFilterForm-2-' + index + '" action="/secmon/web/security-events/add-attribute-to-filter" method="post">\
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />\
            <input type="hidden" name="operator" value="AND">\
            <input type="hidden" name="negation" value=true>\
            <input type="hidden" name="value" value="' + cellContent + '">\
            <input type="hidden" name="column" value="' + column + '">\
            <input type="submit" value="+ AND is NOT ' + cellContent + '">\
            </form>\
            <form id="addAttributeToFilterForm-3-' + index + '" action="/secmon/web/security-events/add-attribute-to-filter" method="post">\
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />\
            <input type="hidden" name="operator" value="OR">\
            <input type="hidden" name="negation" value=false>\
            <input type="hidden" name="value" value="' + cellContent + '">\
            <input type="hidden" name="column" value="' + column + '">\
            <input type="submit" value="+ OR is ' + cellContent + '">\
            </form>\
            </div>').appendTo(cell);
    }

    function getColumnsKeyNames() {
        var objectColumns = <?php echo json_encode($colsDown); ?>;
        return Object.keys(objectColumns);
    }

    function validateColumnName(selectedColumn, objectColumns) {
        var columns = Object.values(objectColumns);

        if(columns.includes(selectedColumn))
            return true;
        else
            return false;
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
        var objectColumns = <?php echo json_encode($colsDown); ?>;
        var newColumnName = Object.keys(objectColumns).find(key => objectColumns[key] === newColumn);
        var selectedColumns = extractColumnsFromChips();
        return selectedColumns.includes(newColumnName);
    }

    function showAbsoluteTimeForm() {
        $("#absoluteTimeForm").show();
        $("#relativeTimeForm").hide();
    }

    function showRelativeTimeForm() {
        $("#absoluteTimeForm").hide();
        $("#relativeTimeForm").show();
    }

    function updateAbsoluteTimeFilter(from, to){
        $.ajax({
            url: "update-time-filter",
            method: "POST",
            data:
            {
                _crsf: $('meta[name="csrf-token"]').attr('content'),
                timeFilterType: 'absolute',
                absoluteTimeFrom: from,
                absoluteTimeTo: to,
                relativeTime: ''
            },
            datatype: "json"
        })
        .done(function(msg) {
                $("#result").html(msg);
        })
        .fail(function(jqXHR, textStatus) {
            $("#result").html("Request failed: " + textStatus);
        });
    }
</script>