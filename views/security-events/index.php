<?php

use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\cmenu\ContextMenu;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\SecurityEventsPage;
use app\models\Filter;
use app\models\FilterRule;
use \app\models\SecurityEvents;
use \app\controllers\FilterController;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SecurityEventsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->registerJsFile('@web/js/security-events-page.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js');

$this->params['title'] = 'Security Events';
$loggedUserId = Yii::$app->user->getId();
$securityEventsPage = SecurityEventsPage::findOne(['user_id' => $loggedUserId]);
$autoRefresh = $securityEventsPage->auto_refresh;
$refreshTime = $securityEventsPage->refresh_time;
$dataColumns = explode(",", $securityEventsPage->data_columns);
array_push($dataColumns, ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn', 'template'=>'{view}']);
$filters = FilterController::getFiltersOfUser($loggedUserId);
$selectedFilterId = SecurityEventsPage::findOne(['user_id' => $loggedUserId])->getAttribute('filter_id');
$selectedFilter = Filter::findOne(['id' => $selectedFilterId]);
$filter = new Filter();
$colsDown = SecurityEvents::getColumnsDropdown();

//$columns = array_keys(\app\models\SecurityEvents::columns());
$columns = explode(",", $securityEventsPage->data_columns);

// If $autoRefresh is set to true, then set interval for content update
if($autoRefresh)
{
    $this->registerJs('
    var refreshString = "' . $refreshTime .'" ;
    
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

<div class="security-events-page-panel">
    <div class="row">
        <div class="col" style="width:33%;">
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

        <div class="col">
            <label class="active" for="name">Refresh Time</label>
            <?= Html::beginForm(['update-refresh-time'],'post'); ?>
                <?= Html::activeInput('text', $securityEventsPage, 'refresh_time', ['placeholder' => 'nY/nM/nW/nD/nH/nm/nS']) ?>
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
            <?= Html::endForm(); ?>
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


<a href="#modal<?= $securityEventsPage->id ?>" class="btn-floating waves-effect waves-light btn-small blue"
    style="position:absolute; top: 30px; right: 40px; display: 'block' ?>" id="contentEdit">
    <i class="material-icons">edit</i>
</a>

<!-- Modal Structure -->
<div class="modal" id="modal<?= $securityEventsPage->id ?>">
    <div class="modal-content">
        <form action="#" id="contentSettingsForm<?= $securityEventsPage->id ?>">
            <div class="row">
                <input name="dataTypeParameter" id="componentDataTypeParameter<?= $securityEventsPage->id ?>" hidden />
                <p class="caption">Table columns</p>
                <div id="chipsTable<?= $securityEventsPage->id ?>" class="chips chips-table" data-id="<?= $securityEventsPage->id ?>"
                        data-table-columns="<?= !empty($securityEventsPage->data_columns) ? $securityEventsPage->data_columns : 'id,datetime,device_host_name,application_protocol'
                        ?>">
                    <?php foreach ($columns as $column ) : ?>
                        <div class="chip">
                            <?= $column ?>
                            <i class="close material-icons">close</i>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class=" input-field col s11">
                    <select id="columnsSelect<?= $securityEventsPage->id ?>">
                        <?php foreach ($colsDown as $key1 => $val1) : ?>
                        <option value="<?= $key1 ?>"><?= $val1 ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Add column</label>
                </div>

                <div class="input-field col s1">
                    <div class="help-block left-align">
                        <a href="#" data-type="columnsSelectAdd" data-id="<?= $securityEventsPage->id ?>" class="btn-floating btn-small waves-effect waves-light red"
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
            <button data-id="<?= $securityEventsPage->id ?>" data-action="saveComponentContent" class="modal-action modal-close waves-effect waves-green btn-flat">Save</button>
            <button class=" modal-close waves-effect waves-green btn-flat">Cancel</button>
        </div>
    </div>
</div>

<div class="chips-table" id="chipstable">
    <?php foreach ($columns as $column ) : ?>
        <div class="chip" value="<?= $column ?>">
            <?= $column ?>
            <i class="close material-icons">close</i>
        </div>
    <?php endforeach; ?>
</div>

<div class="input-field col s11">
    <label class="active" for="name">Add Column</label>
    <select class="form-select" id="selectColumnDropdown" aria-label="Default select example">
    <?php foreach ($colsDown as $key => $value) : ?>
        <option value="<?= $key ?>"><?= $value ?></option>
    <?php endforeach; ?>
    </select>
</div>

<a class="btn btn-primary" id="addColumn" href="#">Add column</a>
<a class="btn btn-primary" id="saveSelectedColumns" href="#">Save selected columns</a>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>

<script>
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
    
    // Create new column chip based on selected from dropdown list
    $("#addColumn").on("click", function (event) {
        // Get selected Column value
        var element = document.getElementById("selectColumnDropdown");
        var value = element.value;

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
        const chipstable = document.getElementById("chipstable");
        const elements = chipstable.getElementsByClassName('chip');
        var chips = Array.prototype.slice.call( elements );
        var selectedColumns = [];
        chips.forEach(chip => selectedColumns.push(chip.getAttribute('value')));
        $.post("/secmon/web/security-events/update-selected-columns", {value:selectedColumns});
    });

</script>

<?php
/*
'columns' => [
                    [
                            'class' => 'yii\grid\SerialColumn',
                    ],
                    'id',
                    [
                        'attribute' => 'datetime',
                        'value' => 'datetime',
                        'format' => 'raw',
                        'filter' => \macgyer\yii2materializecss\widgets\form\DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'datetime',
                            'clientOptions' => [
                                'format' => 'yyyy-mm-dd'
                            ]
                        ])
                    ],
                    'device_host_name',
                    'type',
                    'cef_name',
                    [
                        'attribute' => 'cef_severity',
                        'value' => 'cef_severity',
                        'contentOptions' => function ($dataProvider, $key, $index, $column) {
                            $array = [
                                ['id' => '1', 'data' => '#00DBFF'],
                                ['id' => '2', 'data' => '#00DBFF'],
                                ['id' => '3', 'data' => '#00FF00'],
                                ['id' => '4', 'data' => '#00FF00'],
                                ['id' => '5', 'data' => '#FFFF00'],
                                ['id' => '6', 'data' => '#FFFF00'],
                                ['id' => '7', 'data' => '#CC5500'],
                                ['id' => '8', 'data' => '#CC5500'],
                                ['id' => '9', 'data' => '#FF0000'],
                                ['id' => '10', 'data' => '#FF0000'],
                            ];
                            if (0 < $dataProvider->cef_severity && $dataProvider->cef_severity < 11){
                                $map = ArrayHelper::map($array, 'id', 'data');
                                return ['style' => 'background-color:'.$map[$dataProvider->cef_severity]];
                            } else {
                                return ['style' => 'background-color:#FFFFFF'];
                            }
                        }
                    ],
                    'source_address',
                    'application_protocol',
                    [
                        'class' => '\dosamigos\grid\columns\BooleanColumn',
                        'attribute' => 'analyzed',
                        'treatEmptyAsFalse' => true
                    ],
                    ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn', 'template'=>'{view}'],
                ],
*/
?>