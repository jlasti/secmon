<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\View\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJsFile('@web/js/packery.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('@web/js/d3.v4.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('@web/js/draggabilly.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('@web/js/view.js', ['depends' => 'yii\web\YiiAsset']);

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
          </ul>
        </div>
    </div>

    <?php foreach ($views as $view): ?>
        <?php printf("<div class='grid' id='%s'>", $view->id); ?>

        <div id="component_1" class="grid-item card">
            <div class="card-content">
                <div class="card-header">
                    <span class="card-title activator grey-text text-darken-4">First with graph<i class="material-icons right">more_vert</i></span>
                </div>
                <div class="card-body">
                    <svg width="400" height="500"></svg>
                </div>
            </div>
            <div class="card-reveal">
                <div class="card-header">
                    <span class="card-title grey-text text-darken-4">First with graph - options<i class="material-icons right">close</i></span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col s12">
                            <label class="active">Select width</label>
                            <select class="widthSelect" data-id="component_1">
                                <option value="">25%</option>
                                <option value="width2">50%</option>
                                <option value="width3">75%</option>
                                <option value="width4">100%</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="component_2" class="grid-item card">
            <div class="card-content">
                <div class="card-header">
                    <span class="card-title activator grey-text text-darken-4">Second<i class="material-icons right">more_vert</i></span>
                </div>
                <div class="card-body">

                </div>
            </div>
            <div class="card-reveal">
                <div class="card-header">
                    <span class="card-title grey-text text-darken-4">Second - options<i class="material-icons right">close</i></span>
                </div>
                <div class="card-body">
                    <p>Here is some more information about this product that is only revealed once clicked on.</p>
                </div>
            </div>
        </div>

        <div id="component_3" class="grid-item card">
            <div class="card-content">
                <div class="card-header">
                    <span class="card-title activator grey-text text-darken-4">Third<i class="material-icons right">more_vert</i></span>
                </div>
                <div class="card-body">
                    <p>Obsah hocijaky <a href="#">This is a link</a></p>
                    <p>Obsah hocijaky <a href="#">This is a link</a></p>
                    <p>Obsah hocijaky <a href="#">This is a link</a></p>
                </div>
            </div>
            <div class="card-reveal">
                <div class="card-header">
                    <span class="card-title grey-text text-darken-4">Third - options<i class="material-icons right">close</i></span>
                </div>
                <div class="card-body">
                    <p>Here is some more information about this product that is only revealed once clicked on.</p>
                </div>
            </div>
        </div>
        </div>
    <?php endforeach; ?>
</div>