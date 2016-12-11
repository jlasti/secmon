<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\View */

$this->title = Yii::t('app', 'Create View');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Views'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="view-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
