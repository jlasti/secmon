<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SecRules\SecRulesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sec Rules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sec-rules-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Sec Rules', ['create'], ['class' => 'btn btn-success']) ?>
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
