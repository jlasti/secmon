<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        'js/packery.min.js',
        'js/draggabilly.min.js',
        'js/site.js',
        'js/d3.v4.min.js',
        'js/customGraphs.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
        'macgyer\yii2materializecss\assets\MaterializeAsset',
        'macgyer\yii2materializecss\assets\MaterializePluginAsset',
    ];
}
