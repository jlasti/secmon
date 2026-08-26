<?php
use yii\helpers\Html;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    
    <?php $this->head() ?> 
</head>
<body>
    <?php $this->beginBody() ?>

    <div id="root"><?= $content ?></div>
    
    <?php
    // Use file modification time to bust the browser cache on each new build
    $bundleBase = Yii::getAlias('@webroot/js/dist');
    $cssVersion = @filemtime($bundleBase . '/dashboard-bundle.css');
    $jsVersion = @filemtime($bundleBase . '/dashboard-bundle.js');
    $this->registerCssFile('@web/js/dist/dashboard-bundle.css?v=' . $cssVersion);
    $this->registerJsFile('@web/js/dist/dashboard-bundle.js?v=' . $jsVersion, [
        'position' => \yii\web\View::POS_END,
        'depends' => [] 
    ]);
    ?>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>