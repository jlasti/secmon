<?php

use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\View\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJsFile('@web/js/packery.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('@web/js/d3.v4.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('@web/js/draggabilly.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('@web/js/view.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('@web/js/jquery-ui.min.js', ['depends' => 'yii\web\YiiAsset']);
//$this->registerJs(sprintf('$(document).ready(function(){DrawLineGraph(%s);});', $graph), \yii\web\View::POS_END);
$select = '<div class="row"><div class="col s12 m6 l4"><select id="dashboard">';

foreach($views as $view)
{
    $select .= sprintf("<option value='%s' %s>%s</option>", $view->id, ($view->active == 1 ? 'selected' : ''), $view->name );
}

$comps = "";
$select .= '</select></div></div>';

$this->params['title'] = $select;
?>

<div class="view-index">

    <?php 
        //die(var_dump($views));
     ?>

    <div class="main-actions centered-horizontal">
        <?= Html::a("<i class='material-icons'>add_to_queue</i>" . Yii::t('app', 'Create View'), ['create'], ['class' => 'btn-floating waves-effect waves-light btn-large red']) ?>
        <?= Html::a("<i class='material-icons'>edit</i>" . Yii::t('app', 'Update'), ['update', 'id' => 0], ['id' => 'editBtn', 'class' => 'btn-floating waves-effect waves-light btn-large blue']) ?>
        <?= Html::a("<i class='material-icons'>delete</i>" . Yii::t('app', 'Delete'), 
            ['delete', 'id' => 0],
            ['id' => 'removeBtn',
             'class' => 'btn-floating waves-effect waves-light btn-large red',
             'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this dashboard?'),
                'method' => 'post',
                ],
            ]) ?>
    </div>

    <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
        <a class="btn-floating btn-large red" id="addComponentBtn">
            <i class="material-icons">add_box</i>
        </a>
    </div>

    <?php foreach ($views as $view): ?>
        <?php 
            printf("<div class='grid invisible' id='grid_%s'><div class='grid-sizer'></div><div class='gutter-sizer'></div>", $view->id);
            $components = $view->getViewComponents()->all();
            
            foreach ($components as $component) 
            {
                if ($component->filter_id != null)
                {
                    if ($comps != "")
                        $comps .= ", ";

                    $comps .= $component->id;
                }

                echo \app\widgets\ComponentWidget::widget(['data' => compact('component')]);    
            }
        ?>
        </div>
    <?php endforeach; ?>
</div>

<?php

$columns = array_keys(\app\models\SecurityEvents::columns());

$this->registerJs(
    sprintf("var global = (function () { return this; })();
        var comps = [ %s ];
    
        global.views({
            changeView: '%s',
            createComponent: '%s',
            deleteComponent: '%s',
            updateComponent: '%s',
            updateOrder: '%s',
            getRefreshTimes: '%s',
            updateComponentSettings: '%s',
            deleteComponentSettings: '%s',
            updateComponentContent: '%s',
            activeComponentIds: comps,
            tableColumns: { \"%s\" : null }
        });",
        $comps,
        Url::to(["view/change-view"]),
        Url::to(["view/create-component"]),
        Url::to(["view/delete-component"]),
        Url::to(["view/update-component"]),
        Url::to(["view/update-order-of-components"]),
        Url::to(["view/get-refresh-times"]),
        Url::to(["filter/add-filter-to-component"]),
        Url::to(["filter/remove-filter-from-component"]),
        Url::to(["filter/get-component-content"]),
        join('" : null, "', $columns)
    )
);

?>