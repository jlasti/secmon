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
$this->params['title'] = 'Dashboard';
?>
<div class="view-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="main-actions centered-horizontal">
        <?= Html::a("<i class='material-icons'>add</i>" . Yii::t('app', 'Create View'), ['create'], ['class' => 'btn-floating waves-effect waves-light btn-large red']) ?>
    </div>

    <div class="row">
        <div class="col s12 z-depth-1">
          <ul>
            <li class="col s3">
                <div class="input-field col s12">
                    <select id="dashboard">
                      <option value="1">Dashboard 1</option>
                      <option value="2">Dashboard 2</option>
                      <option value="3">Dashboard 3</option>
                    </select>
                    <label>Select Dashboard</label>
                </div>
            </li>
          </ul>
        </div>
    </div>

    <div class="grid" id="1">
      <div class="grid-item card width2">
        <div class="card-content">
            <div class="card-header">
                <span class="card-title activator grey-text text-darken-4">First with graph<i class="material-icons right">more_vert</i></span>
            </div>
            <div class="card-body">
                <svg width="960" height="500"></svg>
            </div>
        </div>
        <div class="card-reveal">
            <div class="card-header">
                <span class="card-title grey-text text-darken-4">First with graph - options<i class="material-icons right">close</i></span>
            </div>
            <div class="card-body">
                <p>Here is some more information about this product that is only revealed once clicked on.</p>
            </div>
        </div>
      </div>

      <div class="grid-item card width2">
        <div class="card-content">
            <div class="card-header">
                <span class="card-title activator grey-text text-darken-4">Second<i class="material-icons right">more_vert</i></span>
            </div>
            <div class="card-body">
                <p>Obsah hocijaky <a href="#">This is a link</a></p>
                <p>Obsah hocijaky <a href="#">This is a link</a></p>
                <p>Obsah hocijaky <a href="#">This is a link</a></p>
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

      <div class="grid-item card">
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

    <div class="grid" id="2">
        <div class="grid-item card width2">
        <div class="card-content">
            <div class="card-header">
                <span class="card-title activator grey-text text-darken-4">Second page<i class="material-icons right">more_vert</i></span>
            </div>
            <div class="card-body">
                <p>Obsah hocijaky <a href="#">This is a link</a></p>
                <p>Obsah hocijaky <a href="#">This is a link</a></p>
                <p>Obsah hocijaky <a href="#">This is a link</a></p>
            </div>
        </div>
        <div class="card-reveal">
            <div class="card-header">
                <span class="card-title grey-text text-darken-4">Second page - options<i class="material-icons right">close</i></span>
            </div>
            <div class="card-body">
                <p>Here is some more information about this product that is only revealed once clicked on.</p>
            </div>
        </div>
      </div>
    </div>    

    <div class="grid" id="3">
        <div class="grid-item card width2">
        <div class="card-content">
            <div class="card-header">
                <span class="card-title activator grey-text text-darken-4">Third page<i class="material-icons right">more_vert</i></span>
            </div>
            <div class="card-body">
                <p>Obsah hocijaky <a href="#">This is a link</a></p>
                <p>Obsah hocijaky <a href="#">This is a link</a></p>
                <p>Obsah hocijaky <a href="#">This is a link</a></p>
            </div>
        </div>
        <div class="card-reveal">
            <div class="card-header">
                <span class="card-title grey-text text-darken-4">Third page - options<i class="material-icons right">close</i></span>
            </div>
            <div class="card-body">
                <p>Here is some more information about this product that is only revealed once clicked on.</p>
            </div>
        </div>
      </div>
    </div>    
    
<!--<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'user_id',
            'active',

            ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?>*-->

</div>
