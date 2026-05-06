<?php

namespace app\models\Dashboard;

use Yii;

class WidgetLayout extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'widget_layout';
    }

    public function rules()
    {
        return [
            [['widget_id', 'x', 'y', 'w', 'h'], 'required'],
            [['widget_id', 'x', 'y', 'w', 'h'], 'integer'],
            [['widget_id'], 'exist', 'skipOnError' => true, 'targetClass' => DashboardWidget::className(), 'targetAttribute' => ['widget_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'widget_id' => 'Widget ID',
            'x' => 'X Position',
            'y' => 'Y Position',
            'w' => 'Width',
            'h' => 'Height',
        ];
    }

    public function getWidget()
    {
        return $this->hasOne(DashboardWidget::className(), ['id' => 'widget_id']);
    }
}
