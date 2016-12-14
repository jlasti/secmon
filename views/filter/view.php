<?php

use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\data\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Filter */

$this->params['title'] = 'Filter: ' . $model->name;
?>
<div class="filter-view">

    <div class="main-actions centered-horizontal">
        <?= Html::a("<i class='material-icons'>edit</i>" . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-floating waves-effect waves-light btn-large blue']) ?>
        <?= Html::a("<i class='material-icons'>delete</i>" . Yii::t('app', 'Delete'), 
            ['delete', 'id' => $model->id],
            ['class' => 'btn-floating waves-effect waves-light btn-large red',
             'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'name',
        ],
    ]) ?>

	<table class="table table-striped table-bordered">
		<tr><th>Rules</th></tr>

		<?php
        $i = 0;

		foreach($model->rules as $rule)
		{
			echo sprintf('<tr><td>%s %s %s %s</td></tr>', $i == 0 ? '' : $rule->logic_operator, $rule->type, $rule->operator, $rule->value);

            $i++;
		}
		?>
	</table>

</div>
