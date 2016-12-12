<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\View\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Dashboard';
?>
<div class="view-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="main-actions centered-horizontal">
        <?= Html::a("<i class='material-icons'>add</i>" . Yii::t('app', 'Create View'), ['create'], ['class' => 'btn-floating waves-effect waves-light btn-large red']) ?>
    </div>

    <div class="grid">
      <div class="blue lighten-4 grid-item">
          <svg width="960" height="500"></svg>
      </div>
      <div class="blue lighten-4 grid-item width2">...</div>
      <div class="blue lighten-4 grid-item">...</div>
    </div>
    
<?php Pjax::begin(); ?>    <?= GridView::widget([
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
<?php Pjax::end(); ?></div>
