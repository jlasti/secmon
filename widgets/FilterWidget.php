<?php
/**
 * Created by PhpStorm.
 * User: pukes
 * Date: 26.3.2017
 * Time: 14:24
 */

namespace app\widgets;


class FilterWidget extends \yii\base\Widget
{
    public $data;

    public function run()
    {
        return $this->renderFile('@app/widgets/filterWidgetView.php', $this->data);
    }
}