<?php

use yii\helpers\Html;
<<<<<<< HEAD
use yii\grid\GridView;
=======
use macgyer\yii2materializecss\widgets\grid\GridView;
>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159

/* @var $this yii\web\View */
/* @var $searchModel app\models\SecRule\SecRuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

<<<<<<< HEAD
$this->title = 'Sec Rules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sec-rule-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Sec Rule', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
=======
$this->params['title'] = 'Sec Rules';
?>
<div class="sec-rule-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="main-actions centered-horizontal">
        <?= Html::a("<i class='material-icons'>add</i>" . Yii::t('app', 'Create Sec Rule'), ['create'], ['class' => 'btn-floating waves-effect waves-light btn-large red']) ?>
    </div>
    
>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'content',

<<<<<<< HEAD
            ['class' => 'yii\grid\ActionColumn'],
=======
            ['class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn'],
>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159
        ],
    ]); ?>
</div>
