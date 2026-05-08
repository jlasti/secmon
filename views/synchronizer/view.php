<?php
use macgyer\yii2materializecss\widgets\data\DetailView;
use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\helpers\Html;
use app\models\MispAttributes;
use app\models\MispSettings;
$mispUrl = MispSettings::getSettings()->misp_url;

/* @var $this yii\web\View */
/* @var $model app\models\MispEvents */
/* @var $attributesProvider yii\data\ActiveDataProvider */

$this->params['title'] = 'MISP Event: ' . $model->event_uuid;
$attributesCount = $attributesProvider->getTotalCount();
?>

<div class="main-actions centered-horizontal">
    <?= Html::a("<i class='material-icons'>delete</i> Delete", ['delete', 'id' => $model->event_id], [
        'class' => 'btn-floating waves-effect waves-light btn-large red',
        'data' => [
            'confirm' => 'Are you sure you want to delete this MISP event?',
            'method' => 'post',
        ],
    ]) ?>
</div>

<div class="misp-event-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'event_id',
            'event_uuid',
            'creator_org',
            [
                'attribute' => 'event_uuid',
                'format' => 'raw',
                'value' => function ($model) use ($mispUrl) {
                    if ($model->event_uuid && !empty($mispUrl)) {
                        $url = rtrim($mispUrl, '/') . '/events/view/' . $model->event_uuid;
                        return Html::a(
                            Html::encode($model->event_uuid),
                            $url,
                            ['target' => '_blank']
                        );
                    }
                    return $model->event_uuid ?: '<span class="grey-text">(nevyplnené)</span>';
                }
            ],
            [
                'attribute' => 'analysis',
                'value' => function($model) {
                    $statuses = [0 => 'Initial', 1 => 'Ongoing', 2 => 'Completed'];
                    return $statuses[$model->analysis] ?? $model->analysis;
                }
            ],
            'timestamp:datetime',
            [
                'attribute' => 'tags',
                'format' => 'raw',
                'value' => function ($model) {
                    if (empty($model->tags)) {
                        return '<span class="grey-text">No tags</span>';
                    }
                    $tagsArray = @json_decode($model->tags, true);
                    if (json_last_error() !== JSON_ERROR_NONE || !is_array($tagsArray)) {
                        return Html::encode($model->tags);
                    }
                    $chips = [];
                    foreach ($tagsArray as $tag) {
                        $chips[] = '<span class="chip">' . Html::encode($tag) . '</span>';
                    }
                    return implode(' ', $chips);
                }
            ],
            'is_sent:boolean',
            'sent_at:datetime',
            'created_at:datetime',
        ],
    ]) ?>
</div>

<ul class="collapsible">
    <li>
      <div class="collapsible-header light-blue accent-4" style="font-size:20px; color: white;">
          <i class="material-icons">list</i> Attributes (<?= $attributesCount ?>)
      </div>
      <div class="collapsible-body">
          <?= GridView::widget([
            'dataProvider' => $attributesProvider,
            'summary' => '',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'attribute_id',
                'type',
                'category',
                'value',
                [
                    'attribute' => 'to_ids',
                    'label' => 'IDS',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->to_ids
                            ? '<span class="green-text"><i class="material-icons">check</i></span>'
                            : '<span class="grey-text"><i class="material-icons">close</i></span>';
                    }
                ],
                [
                    'attribute' => 'comment',
                    'format' => 'ntext',
                    'contentOptions' => ['style' => 'max-width:250px; white-space: normal; word-wrap: break-word;'],
                ],
                [
                    'attribute' => 'timestamp',
                    'format' => 'datetime',
                    'label' => 'Created',
                ],
                [
                    'attribute' => 'first_seen',
                    'format' => 'datetime',
                    'label' => 'First Seen',
                    'visible' => false,
                ],
                [
                    'attribute' => 'last_seen',
                    'format' => 'datetime',
                    'label' => 'Last Seen',
                    'visible' => false,
                ],
                [
                    'attribute' => 'disable_correlation',
                    'format' => 'boolean',
                    'label' => 'Corr. Off',
                    'visible' => false,
                ],
                [
                    'attribute' => 'tags',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if (empty($model->tags)) {
                            return '';
                        }
                        $tagsArray = @json_decode($model->tags, true);
                        if (json_last_error() !== JSON_ERROR_NONE || !is_array($tagsArray)) {
                            return 'Invalid tag data';
                        }
                        $tagLabels = [];
                        foreach ($tagsArray as $tag) {
                            $tagLabels[] = '<span class="chip">' . Html::encode($tag) . '</span>';
                        }
                        return implode('', $tagLabels);
                    },
                ],
            ],
        ]); ?>
      </div>
    </li>
</ul>