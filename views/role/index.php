<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\RoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Roles';
?>
<div class="role-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="main-actions centered-horizontal">
        <?= Html::a("<i class='material-icons'>add</i>" . Yii::t('app', 'Create Role'), ['create'], ['class' => 'btn-floating waves-effect waves-light btn-large red']) ?>
    </div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'slug',

            ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
