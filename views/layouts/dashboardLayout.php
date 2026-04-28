<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;

// NOTE: DO NOT register your default AppAsset here!
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
    $this->registerCssFile('@web/js/dist/dashboard-bundle.css');
    $this->registerJsFile('@web/js/dist/dashboard-bundle.js', [
        'position' => \yii\web\View::POS_END,
        'depends' => [] 
    ]);
    ?>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>