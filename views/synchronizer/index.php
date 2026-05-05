<?php
use yii\helpers\Html;
use macgyer\yii2materializecss\widgets\grid\GridView;
use yii\widgets\ActiveForm;
use app\models\MispSettings;

$this->params['title'] = 'Synchronization';

$settings = MispSettings::getSettings();
$mispUrl = $settings->misp_url ?? '';
$isLocal = Yii::$app->request->get('local');

$this->registerCss("
.collapsible-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.module-title {
    font-size: 1.5rem;
    font-weight: 300;
}
.module-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.bulk-actions {
    margin: 15px 0;
    display: flex;
    gap: 10px;
}
.btn i.left {
    float: none !important;
    margin-right: 8px;
    vertical-align: middle;
    line-height: inherit;
}
.module-actions .btn,
.bulk-actions .btn {
    display: inline-flex;
    align-items: center;
}
.module-actions .btn i,
.bulk-actions .btn i {
    line-height: 1;
}
.info-note {
    margin: 10px 0;
    color: #757575;
}
");
?>

<ul class="collapsible">
    <li class="active">
        <div class="collapsible-header">
            <span class="module-title">
                <i class="material-icons left">security</i>MISP Threat Intelligence
            </span>
            <div class="module-actions">
                <?= Html::a(
                    '<i class="material-icons left">cloud_download</i>Sync',
                    \yii\helpers\Url::to(['sync-now']),
                    [
                        'class' => 'btn waves-effect waves-light red sync-btn',
                        'title' => Yii::t('app', 'Incremental sync from MISP'),
                    ]
                ) ?>
                <?= Html::a(
                    '<i class="material-icons left">send</i>Export',
                    \yii\helpers\Url::to(['export-now']),
                    [
                        'class' => 'btn waves-effect waves-light blue export-btn',
                        'title' => Yii::t('app', 'Send unsent events to MISP'),
                    ]
                ) ?>
                <?= Html::a(
                    '<i class="material-icons left">settings</i>Settings',
                    ['settings'],
                    [
                        'class' => 'btn waves-effect waves-light grey',
                        'title' => Yii::t('app', 'Settings'),
                    ]
                ) ?>
                <?= Html::a(
                    '<i class="material-icons left">filter_list</i>Local',
                    ['index', 'local' => 1],
                    [
                        'class' => 'btn waves-effect waves-light ' . ($isLocal ? 'blue' : 'grey'),
                        'title' => Yii::t('app', 'Local only'),
                    ]
                ) ?>
                <?= Html::a(
                    '<i class="material-icons left">list</i>All',
                    ['index'],
                    [
                        'class' => 'btn waves-effect waves-light ' . (!$isLocal ? 'blue' : 'grey'),
                        'title' => Yii::t('app', 'All'),
                    ]
                ) ?>
            </div>
        </div>
        <div class="collapsible-body">
            <div class="misp-event-index">
                <?php $form = ActiveForm::begin(['id' => 'bulk-action-form', 'action' => ['bulk-action']]); ?>
                <?= Html::hiddenInput('action', '', ['id' => 'bulk-action-input']) ?>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'name' => 'selection',
                            'checkboxOptions' => function ($model) {
                                return ['value' => $model->event_id];
                            },
                        ],
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'event_uuid',
                            'label' => 'Event UUID',
                            'format' => 'raw',
                            'value' => function ($model) use ($mispUrl) {
                                if ($model->event_uuid) {
                                    if (!empty($mispUrl)) {
                                        $url = rtrim($mispUrl, '/') . '/events/view/' . $model->event_uuid;
                                        return Html::a(
                                            '<i class="material-icons tiny">open_in_new</i> ' . Html::encode($model->event_uuid),
                                            $url,
                                            ['target' => '_blank', 'title' => 'View in MISP']
                                        );
                                    }
                                    return Html::encode($model->event_uuid);
                                }
                                return '';
                            }
                        ],
                        'creator_org:text:Organization',
                        [
                            'attribute' => 'timestamp',
                            'format' => 'datetime',
                            'label' => 'Date',
                        ],
                        'threat_level:integer:Threat level',
                        'analysis:integer:Analysis',
                        [
                            'attribute' => 'is_sent',
                            'label' => 'Synced',
                            'format' => 'raw',
                            'value' => function ($model) {
                                if ($model->event_uuid) {
                                    return '<span class="green-text"><i class="material-icons">check</i></span>';
                                }
                                if ($model->is_sent) {
                                    return '<span class="yellow-text text-darken-2"><i class="material-icons">warning</i></span>';
                                }
                                return '<span class="grey-text"><i class="material-icons">close</i></span>';
                            },
                        ],
                        [
                            'attribute' => 'event_uuid',
                            'label' => 'Origin',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $tags = [];
                                if ($model->tags) {
                                    $decoded = @json_decode($model->tags, true);
                                    if (is_array($decoded)) {
                                        $tags = $decoded;
                                    }
                                }
                                
                                if (in_array('secmon', $tags)) {
                                    return '<span class="green-text"><i class="material-icons">create</i> SecMon</span>';
                                }
                                
                                if ($model->event_uuid) {
                                    return '<span class="blue-text"><i class="material-icons">cloud_download</i> MISP</span>';
                                }
                                
                                return '<span class="green-text"><i class="material-icons">create</i> SecMon</span>';
                            }
                        ],
                        [
                            'class' => 'macgyer\yii2materializecss\widgets\grid\ActionColumn',
                            'template' => '{view} {not-export} {delete}',
                            'buttons' => [
                                'not-export' => function ($url, $model, $key) {
                                    if ($model->event_uuid !== null) {
                                        return '';
                                    }
                                    $icon = $model->is_sent ? 'check_circle' : 'block';
                                    $title = $model->is_sent ? 'Mark for export' : 'Do not export';
                                    return Html::a(
                                        '<i class="material-icons">' . $icon . '</i>',
                                        $url,
                                        [
                                            'title' => $title,
                                            'class' => 'not-export-btn',
                                        ]
                                    );
                                },
                            ],
                            'visibleButtons' => [
                                'not-export' => function ($model) {
                                    return $model->event_uuid === null;
                                },
                            ],
                            'urlCreator' => function ($action, $model, $key, $index) {
                                if ($action === 'view') {
                                    return ['view', 'id' => $model->event_id];
                                }
                                if ($action === 'delete') {
                                    return ['delete', 'id' => $model->event_id];
                                }
                                if ($action === 'not-export') {
                                    return ['toggle-sent', 'id' => $model->event_id];
                                }
                            },
                        ],
                    ],
                ]); ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </li>
</ul>

<?php
$this->registerJs(<<<JS
    var collapsibleElem = $('.collapsible');
    collapsibleElem.collapsible({ accordion: true });

    setTimeout(function() {
        collapsibleElem.collapsible('open', 0);
    }, 50);

    $('.collapsible-header .btn, .collapsible-header a').on('click', function(e) {
        e.stopPropagation();
    });

    $(document).on('click', '.bulk-action-btn', function(e) {
        e.preventDefault();
        var action = $(this).data('action');
        $('#bulk-action-input').val(action);
        $('#bulk-action-form').submit();
    });

    $('.sync-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var url = $(this).attr('href');
        $.post(url)
            .done(function(response) {
                M.toast({html: response.message, classes: 'green'});
                location.reload();
            })
            .fail(function(xhr) {
                var msg = xhr.responseJSON?.message || 'Request failed';
                M.toast({html: msg, classes: 'red'});
            });
    });

    $('.export-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var url = $(this).attr('href');
        $.post(url)
            .done(function(response) {
                M.toast({html: response.message, classes: 'green'});
                location.reload();
            })
            .fail(function(xhr) {
                var msg = xhr.responseJSON?.message || 'Request failed';
                M.toast({html: msg, classes: 'red'});
            });
    });

    $(document).on('click', '.not-export-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.post(url)
            .done(function(response) {
                M.toast({html: response.message, classes: 'green'});
                location.reload();
            })
            .fail(function(xhr) {
                var msg = 'Request failed';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                M.toast({html: msg, classes: 'red'});
            });
    });
JS
);
?>