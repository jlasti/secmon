<?php
namespace app\widgets;

class ComponentWidget extends \yii\base\Widget
{
    public $data;

    public function run()
    {
        return $this->renderFile('@app/widgets/componentWidgetView.php', $this->data);
    }
}