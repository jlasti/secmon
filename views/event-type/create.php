<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Event\EventType */

$this->title = Yii::t('app', 'Create Event Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Event Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
