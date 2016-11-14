<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SecRule\SecRuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'Create Rules';
?>
<div class="sec-rule-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="main-actions centered-horizontal">
        <?= Html::a("<i class='material-icons'>add</i>" . Yii::t('app', 'Create Sec Rule'), ['create'], ['class' => 'btn-floating waves-effect waves-light btn-large red']) ?>
    </div>
    <p>
        <?= Html::a('Create Sec Rule', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'content',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
