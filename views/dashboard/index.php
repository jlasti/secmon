<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;

$this->title = 'Dashboard';

// Register React bundle (JS and CSS)
$this->registerCssFile('@web/js/dist/dashboard-bundle.css', [
]);

$this->registerJsFile('@web/js/dist/dashboard-bundle.js', [
    'position' => \yii\web\View::POS_END
]);

?>

<div class="view-index">
    <!-- React Dashboard Root -->
    <div id="react-dashboard-root"></div>

    <!-- Fallback for browsers without JavaScript -->
    <noscript>
        <div style="padding: 40px; text-align: center; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; margin: 20px;">
            <h3>JavaScript Required</h3>
            <p>This dashboard requires JavaScript to be enabled. Please enable JavaScript in your browser settings.</p>
        </div>
    </noscript>
</div>
