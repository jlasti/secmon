<?= macgyer\yii2materializecss\widgets\grid\GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels' => $filteredData
    ]),
    'layout' => '{items}',
    'columns' => $columns,
]); ?>