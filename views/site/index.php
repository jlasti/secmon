<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

$this->params['title'] = 'Welcome';
?>

<div class="site-index">
    <h2>Text</h2>
    <?php if(Yii::$app->user->isGuest)
    {
        echo Html::a('Login', '@web/site/login', ['class' => 'waves-effect waves-light btn']);
    }
    ?>
</div>
