<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

$this->params['title'] = 'SecMon - Open-Source Security Monitoring Tool';
?>

<div class="site-index">
    <?php if(Yii::$app->user->isGuest)
    {
        echo Html::a('Login', '@web/site/login', ['class' => 'waves-effect waves-light btn']);
    }
    ?>
</div>
