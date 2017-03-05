<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Json;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\View\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJsFile('@web/js/packery.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('@web/js/d3.v4.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('@web/js/draggabilly.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('@web/js/view.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJs(
        sprintf("var global = (function () { return this; })();
            global.views({
                changeView: '%s',
                createComponent: '%s',
                deleteComponent: '%s',
                updateComponent: '%s'
            });",
            Url::to(["view/change-view"]),
            Url::to(["view/create-component"]),
            Url::to(["view/delete-component"]),
            Url::to(["view/update-component"])
        )
);

//$this->registerJs(sprintf('$(document).ready(function(){DrawLineGraph(%s);});', $graph), \yii\web\View::POS_END);

$this->params['title'] = 'Dashboard';
?>

<div class="view-index">

    <?php 
        //die(var_dump($views));
     ?>

    <div class="main-actions centered-horizontal">
        <?= Html::a("<i class='material-icons'>add</i>" . Yii::t('app', 'Create View'), ['create'], ['class' => 'btn-floating waves-effect waves-light btn-large red']) ?>
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

    <div class="row">
        <div class="col s12 z-depth-1">
          <ul>
            <li class="col s3">
                <div class="input-field col s12">
                    <select id="dashboard">
                    <?php foreach($views as $view) 
                    {
                        printf("<option value='%s' %s>%s</option>", $view->id, ($view->active == 1 ? 'selected' : ''), $view->name );
                    }
                    ?>
                    </select>
                    <label>Select Dashboard</label>
                </div>
            </li>
            <li class="col s3">
                 <button class="waves-effect waves-light btn" id="addComponentBtn">Add Component</button>
            </li>
          </ul>
        </div>
    </div>

    <?php foreach ($views as $view): ?>
        <?php 
            printf("<div class='grid' id='grid_%s'>", $view->id);
            $components = $view->getViewComponents()->all();
            
            foreach ($components as $component):
                $options =  Json::decode($component->config);
                printf("<div class='grid-item card %s' id='component_%s'>", $options['width'], $component->id);
        ?>

                <div class="card-content">
                    <div class="card-header">
                        <span class="card-title activator grey-text text-darken-4"><?php  echo $options['name']; ?><i class="material-icons right">more_vert</i></span>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
                <div class="card-reveal">
                    <div class="card-header light-blue accent-4">
                        <span class="card-title white-text"><?php  echo $options['name']; ?> - options<i class="material-icons right">close</i></span>
                    </div>
                    <div class="card-body">
                        <form class="row componentForm"  data-id="<?php  echo $component->id; ?>">
                            <div class="input-field col s12">
                                <label class="active" for="name">Name</label>
                                <input id="name<?php  echo $component->id; ?>" type="text" value="<?php  echo $options['name']; ?>">
                            </div>
                            <div class="input-field col s12">
                                <label class="active">Select width</label>
                                <select id="width<?php  echo $component->id; ?>" class="widthSelect" data-id="component_<?php  echo $component->id; ?>">
                                    <option <?=$options['width'] == '' ? ' selected="selected"' : '';?> value="">25%</option>
                                    <option <?=$options['width'] == 'width2' ? ' selected="selected"' : '';?> value="width2">50%</option>
                                    <option <?=$options['width'] == 'width3' ? ' selected="selected"' : '';?> value="width3">75%</option>
                                    <option <?=$options['width'] == 'width4' ? ' selected="selected"' : '';?> value="width4">100%</option>
                                </select>
                            </div>
                            <div class="input-field col s12">
                                <button class="deleteComponentBtn btn waves-effect waves-light red" data-id="<?php  echo $component->id; ?>">
                                    Delete
                                    <i class="material-icons right">delete</i>
                                </button>
                                <button class="btn waves-effect waves-light green" type="submit">
                                    Save
                                    <i class="material-icons right">save</i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

         <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>