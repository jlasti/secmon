<?php

namespace app\assets;

use yii\web\AssetBundle;

class FlatpickrAsset extends AssetBundle
{
    public $sourcePath = '@bower/flatpickr';
    public $baseUrl = '@web';
    public $css = [ 'dist/ie.css', 'dist/flatpickr.min.css', ];
    public $js = [ 'dist/flatpickr.min.js', ];
}
