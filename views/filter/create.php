<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Filter */

$this->title = Yii::t('app', 'Create Filter');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Filters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="filter-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'rules' => $rules,
    ]) ?>

</div>
