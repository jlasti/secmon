<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Event\EventType */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Event Type',
]) . $model->name;
$this->params['title'] = 'Update ';
?>
<div class="event-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
