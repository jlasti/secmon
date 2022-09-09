<?php

use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\cmenu\ContextMenu;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\models\SecurityEventsPage;
use app\models\Filter;
use \app\controllers\FilterController;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SecurityEventsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Security Events';
$loggedUserId = Yii::$app->user->getId();
$securityEventsPage = SecurityEventsPage::findOne(['user_id' => $loggedUserId]);
$autoRefresh = $securityEventsPage->auto_refresh;
$refreshTime = $securityEventsPage->refresh_time;
$dataColumns = explode(",", $securityEventsPage->data_columns);
array_push($dataColumns, ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn', 'template'=>'{view}']);
$filters = FilterController::getFiltersOfUser($loggedUserId);

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
        <div class="col">
            <label for="appliedFilterId<?= $securityEventsPage->id ?>">Selected Filter</label>
            <select id="appliedFilterId<?= $securityEventsPage->id ?>" name="filterId">
                <option value="" disabled selected>Select filter</option>
                <?php foreach ($filters as $filter):  ?>
                    <option value="<?= $filter->id ?>"<?= $securityEventsPage->filter_id == $filter->id ? " selected='selected'" : "" ?>><?= $filter->name; ?></option>
                <?php endforeach; ?>
            </select>
            <?= Html::submitButton("<i class='material-icons'>done</i>", ['class' => 'btn btn-success', 'title' => 'Apply selected filter']) ?>
            <?= Html::a("<i class='material-icons'>add</i>", ['filter/create'], ['class' => 'btn btn-success', 'title' => 'Create new filter']) ?>
            <?= Html::a("<i class='material-icons'>edit</i>", ['filter/update', 'id' => 1], ['class' => 'btn btn-success', 'title' => 'Edit selected filter']) ?>
            <?= Html::a("<i class='material-icons'>delete</i>", ['index'], ['class' => 'btn btn-danger', 'style' => 'background-color: red;', 'title' => 'Remove selected filter']) ?>
        </div>

        <div class="col">
            <?php $form = ActiveForm::begin(['action' => ['update-refresh-time']]); ?>
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